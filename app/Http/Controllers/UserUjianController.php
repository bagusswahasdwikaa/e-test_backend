<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\User;
use App\Models\UjianUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\UjianNotificationMail;

class UserUjianController extends Controller
{
    /**
     * Menampilkan daftar ujian yang tersedia untuk user login.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        // Ambil ujian yang dikirim ke user login melalui tabel ujian_users
        $ujians = Ujian::whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        // Mapping data supaya konsisten dengan frontend
        $data = $ujians->map(function ($ujian) {
            return [
                'ujian_id'    => $ujian->id, // gunakan primary key standar
                'nama_ujian'  => $ujian->nama,
                'durasi'      => (int) $ujian->durasi,
                'jumlah_soal' => (int) $ujian->jumlah_soal,
                'status'      => $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir),
                'kode_soal'   => $ujian->kode_soal,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Detail ujian user.
     */
    public function show($id): JsonResponse
    {
        $user = Auth::user();

        $ujian = Ujian::where('id', $id)
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        $ujian->status = $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir);

        return response()->json([
            'success' => true,
            'data'    => $ujian,
        ]);
    }

    /**
     * User memulai ujian → buat record kosong di ujian_users.
     */
    public function kerjakan(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujian = Ujian::where('id', $id)
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        $status = $this->tentukanStatus($ujian->tanggal_mulai, $ujian->tanggal_akhir);

        if ($status !== 'Aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak aktif atau sudah berakhir.',
            ], 403);
        }

        // Buat entri ujian_users jika belum ada
        UjianUser::firstOrCreate(
            ['user_id' => $user->id, 'ujian_id' => $ujian->id],
            ['jawaban' => json_encode([])]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dimulai.',
        ]);
    }

    /**
     * Simpan jawaban user.
     */
    public function simpanJawaban(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujianUser = UjianUser::where('user_id', $user->id)
            ->where('ujian_id', $id)
            ->firstOrFail();

        $data = $request->validate([
            'jawaban' => 'required|array',
        ]);

        $ujianUser->update([
            'jawaban' => json_encode($data['jawaban']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan.',
        ]);
    }

    /**
     * Admin kirim undangan ujian ke user via email.
     */
    public function kirimEmail($idUjian, $userId): JsonResponse
    {
        $ujian = Ujian::findOrFail($idUjian);
        $user  = User::findOrFail($userId);

        // Simpan relasi ujian <-> user di tabel pivot ujian_users
        UjianUser::firstOrCreate([
            'ujian_id' => $ujian->id,
            'user_id'  => $user->id,
        ]);

        // Kirim email ke user
        Mail::to($user->email)->send(new UjianNotificationMail(
            $user->name,
            $ujian->nama,
            $ujian->tanggal_mulai,
            $ujian->tanggal_akhir,
            $ujian->kode_soal ?? '-'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Undangan ujian berhasil dikirim ke ' . $user->email,
        ]);
    }

    /**
     * Tentukan status ujian (Aktif / Non Aktif).
     */
    private function tentukanStatus($tanggalMulai, $tanggalAkhir): string
    {
        $tz    = config('app.timezone', 'Asia/Jakarta');
        $now   = Carbon::now($tz);
        $mulai = Carbon::parse($tanggalMulai, $tz);
        $akhir = Carbon::parse($tanggalAkhir, $tz);

        return $now->between($mulai, $akhir) ? 'Aktif' : 'Non Aktif';
    }
}
