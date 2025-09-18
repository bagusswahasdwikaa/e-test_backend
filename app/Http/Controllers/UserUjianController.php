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

class UserUjianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // load ujian + soal + hasilUjian
       $ujians = UjianUser::with(['ujian.soals', 'hasilUjian'])
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'data' => $ujians->map(function ($ujianUser) {
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

    public function kerjakan(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'kode_soal' => 'required|string',
        ]);

        $ujian = Ujian::where('id_ujian', $id)
            ->where('kode_soal', $data['kode_soal'])
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$ujian) {
            throw ValidationException::withMessages([
                'kode_soal' => ['Kode soal tidak valid atau Anda tidak memiliki akses ke ujian ini.'],
            ]);
        }

        if ($ujian->status !== 'Aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak aktif atau sudah berakhir.',
            ], 403);
        }

        UjianUser::firstOrCreate(
            ['user_id' => $user->id, 'ujian_id' => $ujian->id_ujian],
            ['jawaban' => json_encode([])]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dimulai.',
        ]);
    }

    public function getSoalUjian($id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->first();

        if (!$ujianUser) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke ujian ini.',
            ], 403);
        }

        // Selalu decode JSON
        $jawabanUser = json_decode($ujianUser->jawaban ?? '{}', true) ?? [];

        $soals = Soal::where('ujian_id', $id)->with('jawabans')->get();

        $data = $soals->map(function ($soal) use ($jawabanUser) {
            return [
                'soal_id' => $soal->id,
                'pertanyaan' => $soal->pertanyaan,
                'media_url' => $soal->media_url,
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
            'data' => $data,
        ]);
    }

    public function simpanJawaban(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->firstOrFail();

        $data = $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'numeric',
        ]);

        // Simpan sebagai JSON string
        $ujianUser->jawaban = json_encode($data['jawaban']);
        $ujianUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan.',
        ]);
    }

    public function submitUjian(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::with('ujian')
            ->where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->firstOrFail();

        if ($ujianUser->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah disubmit sebelumnya.',
            ], 422);
        }

        $data = $request->validate([
            'jawaban' => 'required|array',
        ]);

        // Jawaban user bisa berupa string atau numeric
        $jawabanArray = collect($data['jawaban'])
            ->mapWithKeys(fn($val, $key) => [(int) $key => $val])
            ->toArray();

        // Simpan jawaban
        $ujianUser->jawaban = json_encode($jawabanArray);
        $ujianUser->save();

        // Koreksi otomatis
        $soals = Soal::with('jawabans')->where('ujian_id', $id)->get();
        $hasilKoreksi = [];
        $jumlahBenar = 0;

        foreach ($soals as $soal) {
            $userAnswer = $jawabanArray[$soal->id] ?? null;
            $isCorrect = false;
            $kunci = null;

            // Jika soal punya pilihan ganda
            if ($soal->jawabans->count() > 0) {
                $kunci = $soal->jawabans->firstWhere('is_correct', true);
                if ($kunci && (string)$userAnswer === (string)$kunci->id) {
                    $isCorrect = true;
                }
            }
            // Jika soal tipe isian (punya field jawaban_benar di tabel soals)
            elseif (!empty($soal->jawaban_benar)) {
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
        $nilai = $jumlahSoal > 0 ? round(($jumlahBenar / $jumlahSoal) * 100, 2) : 0;

        // Update status ujian user
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

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil disubmit dan dikoreksi.',
            'nilai' => $nilai,
            'jumlah_benar' => $jumlahBenar,
            'jumlah_soal' => $jumlahSoal,
            'hasil_koreksi' => $hasilKoreksi,
        ]);
    }
}