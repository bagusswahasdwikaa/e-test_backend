<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\User;
use App\Models\UjianUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UjianNotificationMail;

class UserUjianController extends Controller
{
    /**
     * Menampilkan daftar ujian yang tersedia untuk user login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $ujians = $user->ujians()->with('soals')->get();

        return response()->json([
            'data' => $ujians->map(function ($ujian) {
                return [
                    'ujian_id' => $ujian->id_ujian,
                    'nilai' => $ujian->pivot->nilai,
                    'ujian' => [
                        'id' => $ujian->id_ujian,
                        'nama' => $ujian->nama_ujian,
                        'durasi' => $ujian->durasi,
                        'jumlah_soal' => $ujian->jumlah_soal,
                        'kode_soal' => $ujian->kode_soal,
                        'status' => $ujian->status, 
                    ],
                ];
            }),
        ]);
    }

    /**
     * Detail ujian user.
     */
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
            'data'    => $ujian,
        ]);
    }

    /**
     * User memulai ujian → buat record kosong di ujian_users.
     */
    public function kerjakan(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $ujian = Ujian::where('id_ujian', $id)
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        if ($ujian->status !== 'Aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak aktif atau sudah berakhir.',
            ], 403);
        }

        // Buat entri ujian_users jika belum ada
        UjianUser::firstOrCreate(
            ['user_id' => $user->id, 'ujian_id' => $ujian->id_ujian],
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
}
