<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\UjianUser;
use App\Models\Soal;
use App\Models\HasilUjian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\SertifikatController;
use Carbon\Carbon;

class UserUjianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $ujians = UjianUser::with(['ujian.soals', 'hasilUjian'])
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'data' => $ujians->map(function ($ujianUser) {
                $endTime = $ujianUser->started_at
                    ? Carbon::parse($ujianUser->started_at)->addMinutes($ujianUser->ujian->durasi)
                    : null;

                return [
                    'ujian_id' => $ujianUser->ujian->id_ujian,
                    'nilai' => $ujianUser->nilai,
                    'status_peserta' => $ujianUser->hasilUjian
                        ? $ujianUser->hasilUjian->status
                        : 'Belum Dikerjakan',
                    'ujian' => [
                        'id' => $ujianUser->ujian->id_ujian,
                        'nama' => $ujianUser->ujian->nama_ujian,
                        'durasi' => $ujianUser->ujian->durasi,
                        'jumlah_soal' => $ujianUser->ujian->jumlah_soal,
                        'kode_soal' => $ujianUser->ujian->kode_soal,
                        'status' => $ujianUser->ujian->status,
                    ],
                    'started_at' => $ujianUser->started_at,
                    'end_time' => $endTime,
                ];
            }),
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = Auth::user();

        $ujian = Ujian::where('id_ujian', $id)
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $ujian,
        ]);
    }

    public function kerjakan(Request $request, $id)
    {
        $request->validate([
            'kode_soal' => 'required|string',
        ]);

        $ujian = Ujian::findOrFail($id);

        if ($ujian->kode_soal !== $request->kode_soal) {
            return response()->json([
                'success' => false,
                'message' => 'Kode soal salah.',
            ], 200); // jangan 422 supaya tidak munculin AxiosError merah
        }

        $user = auth()->user();

        $ujianUser = UjianUser::firstOrCreate(
            ['ujian_id' => $ujian->id_ujian, 'user_id' => $user->id]
        );

        // Kalau belum pernah mulai, set started_at dan end_time
        if (!$ujianUser->started_at) {
            $ujianUser->started_at = now();
            $ujianUser->end_time = now()->addMinutes($ujian->durasi);
            $ujianUser->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Ujian dimulai',
            'started_at' => $ujianUser->started_at,
            'end_time' => $ujianUser->end_time,
        ]);
    }

    public function getSoalUjian($id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::with('ujian')->where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->first();

        if (!$ujianUser) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke ujian ini.',
            ], 403);
        }

        // Cek waktu habis
        if ($ujianUser->started_at) {
            $endTime = Carbon::parse($ujianUser->started_at)->addMinutes($ujianUser->ujian->durasi);
            if (now()->gt($endTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu ujian sudah habis.',
                ], 403);
            }
        }

        // Ambil jawaban sementara dari DB
        $jawabanUser = json_decode($ujianUser->jawaban ?? '{}', true) ?? [];

        $soals = Soal::where('ujian_id', $id)->with('jawabans')->get();

        $data = $soals->map(function ($soal) use ($jawabanUser) {
            return [
                'soal_id' => $soal->id,
                'pertanyaan' => $soal->pertanyaan,
                'media_path' => $soal->media_path, // kirim path, bukan url
                'media_type' => $soal->media_type,
                'jawaban_user' => $jawabanUser[$soal->id] ?? null,
                'jawabans' => $soal->jawabans->map(function ($jawaban) {
                    return [
                        'id' => $jawaban->id,
                        'jawaban' => $jawaban->jawaban,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'ujian_id' => $id,
            'started_at' => $ujianUser->started_at,
            'end_time' => $ujianUser->started_at
                ? Carbon::parse($ujianUser->started_at)->addMinutes($ujianUser->ujian->durasi)
                : null,
            'data' => $data,
        ]);
    }

    private function mergeJawaban(array $jawabanLama, array $jawabanBaru): array
    {
        foreach ($jawabanBaru as $soalId => $ans) {
            // Kalau soal belum pernah dijawab atau jawabannya berubah, update
            if (!array_key_exists($soalId, $jawabanLama) || $jawabanLama[$soalId] !== $ans) {
                $jawabanLama[$soalId] = $ans;
            }
        }
        return $jawabanLama;
    }

    public function simpanJawaban(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::with('ujian')
            ->where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->firstOrFail();

        // Cek waktu habis
        if ($ujianUser->started_at) {
            $endTime = Carbon::parse($ujianUser->started_at)->addMinutes($ujianUser->ujian->durasi);
            if (now()->gt($endTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu ujian sudah habis. Jawaban tidak bisa disimpan.',
                ], 403);
            }
        }

        $data = $request->validate([
            'jawaban' => 'required|array',
        ]);

        $jawabanLama = json_decode($ujianUser->jawaban ?? '{}', true) ?? [];
        $jawabanFinal = $this->mergeJawaban($jawabanLama, $data['jawaban']);

        // Hanya simpan kalau ada perubahan
        if ($jawabanFinal !== $jawabanLama) {
            $ujianUser->jawaban = json_encode($jawabanFinal);
            $ujianUser->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Jawaban sementara berhasil disimpan.',
            'jawaban' => $jawabanFinal,
        ]);
    }

    public function submitUjian(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::with('ujian')
            ->where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->firstOrFail();

        $endTime = null;
        if ($ujianUser->started_at) {
            $endTime = Carbon::parse($ujianUser->started_at)->addMinutes($ujianUser->ujian->durasi);
        }

        if ($ujianUser->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah disubmit sebelumnya.',
            ], 422);
        }

        $data = $request->validate([
            'jawaban' => 'required|array',
        ]);

        $jawabanLama = json_decode($ujianUser->jawaban ?? '{}', true) ?? [];
        $jawabanFinal = $this->mergeJawaban($jawabanLama, $data['jawaban']);

        if ($jawabanFinal !== $jawabanLama) {
            $ujianUser->jawaban = json_encode($jawabanFinal);
            $ujianUser->save();
        }

        // Koreksi
        $soals = Soal::with('jawabans')->where('ujian_id', $id)->get();
        $hasilKoreksi = [];
        $jumlahBenar = 0;

        foreach ($soals as $soal) {
            $userAnswer = $jawabanFinal[$soal->id] ?? null;
            $isCorrect = false;
            $kunci = null;

            if ($soal->jawabans->count() > 0) {
                $kunci = $soal->jawabans->firstWhere('is_correct', true);
                if ($kunci && (string)$userAnswer === (string)$kunci->id) {
                    $isCorrect = true;
                }
            } elseif (!empty($soal->jawaban_benar)) {
                $kunci = $soal->jawaban_benar;
                if (
                    is_string($userAnswer) &&
                    mb_strtolower(trim($userAnswer)) === mb_strtolower(trim($kunci))
                ) {
                    $isCorrect = true;
                }
            }

            if ($isCorrect) {
                $jumlahBenar++;
            }

            $hasilKoreksi[] = [
                'soal_id' => $soal->id,
                'jawaban_user' => $userAnswer,
                'jawaban_benar' => $kunci instanceof \App\Models\Jawaban ? $kunci->id : $kunci,
                'is_correct' => $isCorrect,
            ];
        }

        $jumlahSoal = $soals->count();
        $nilai = $jumlahSoal > 0 ? (int) round(($jumlahBenar / $jumlahSoal) * 100) : 0;

        $standarMinimal = $ujianUser->ujian->standar_minimal_nilai ?? 0;
        $jenisUjian = $ujianUser->ujian->jenis_ujian ?? 'PRETEST';

        if ($nilai < $standarMinimal) {
            // Jika belum lulus, reset jawaban untuk ulang
            $ujianUser->update([
                'jawaban' => null,
                'nilai' => null,
                'started_at' => now(),
                'end_time' => now()->addMinutes($ujianUser->ujian->durasi),
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nilai belum memenuhi standar minimal. Silakan ulangi ujian.',
                'nilai' => $nilai,
                'jumlah_benar' => $jumlahBenar,
                'jumlah_soal' => $jumlahSoal,
                'hasil_koreksi' => $hasilKoreksi,
                'standar_minimal' => $standarMinimal,
                'action' => 'ulang',
            ], 200);
        }

        // Jika lulus
        $ujianUser->update([
            'nilai' => $nilai,
            'is_submitted' => true,
            'submitted_at' => now(),
        ]);

        HasilUjian::updateOrCreate(
            ['ujian_user_id' => $ujianUser->id],
            [
                'ujian_user_id' => $ujianUser->id,
                'waktu_ujian_selesai' => now(),
                'nilai' => $nilai,
                'status' => 'Sudah Dikerjakan',
                'nama_ujian' => $ujianUser->ujian->nama_ujian ?? 'Ujian',
            ]
        );

        if ($jenisUjian === 'POSTEST') {
            SertifikatController::generateSertifikat($ujianUser);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil disubmit dan dinyatakan lulus.',
            'nilai' => $nilai,
            'jumlah_benar' => $jumlahBenar,
            'jumlah_soal' => $jumlahSoal,
            'hasil_koreksi' => $hasilKoreksi,
            'standar_minimal' => $standarMinimal,
            'action' => 'selesai',
        ]);
    }

    public function ulangUjian($id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::with('ujian')
            ->where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->firstOrFail();

        // Pastikan ujian POSTEST dan nilainya di bawah standar minimal
        $ujian = $ujianUser->ujian;
        if (!$ujian || $ujian->jenis_ujian !== 'POSTEST') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian ini tidak dapat diulang.',
            ], 403);
        }

        if ($ujianUser->nilai >= $ujian->standar_minimal_nilai) {
            return response()->json([
                'success' => false,
                'message' => 'Nilai sudah memenuhi standar. Tidak perlu mengulang.',
            ], 403);
        }

        // Simpan riwayat lama ke HasilUjian (kalau belum tersimpan)
        HasilUjian::updateOrCreate(
            ['ujian_user_id' => $ujianUser->id],
            [
                'ujian_user_id' => $ujianUser->id,
                'waktu_ujian_selesai' => now(),
                'nilai' => $ujianUser->nilai,
                'status' => 'Ulangan Disimpan',
                'nama_ujian' => $ujian->nama_ujian ?? 'Ujian',
            ]
        );

        // Reset jawaban dan waktu untuk mengulang ujian dari awal
        $ujianUser->update([
            'jawaban' => null,
            'nilai' => null,
            'is_submitted' => false,
            'submitted_at' => null,
            'started_at' => now(),
            'end_time' => now()->addMinutes($ujian->durasi),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian direset. Silakan mulai dari awal.',
            'started_at' => $ujianUser->started_at,
            'end_time' => $ujianUser->end_time,
        ]);
    }
}
