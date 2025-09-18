<?php

namespace App\Http\Controllers;

use App\Models\UjianUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilUjianController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Ambil data ujian yang sudah dikerjakan oleh user
        $ujianUser = UjianUser::with('hasilUjian') // Menggunakan relasi hasilUjian
            ->where('user_id', $user->id)
            ->get();

        // Jika tidak ada data hasil ujian
        if ($ujianUser->isEmpty()) {
            return response()->json([
                'message' => 'Anda belum mengikuti ujian apapun atau hasil ujian belum tersedia.'
            ], 404);
        }

        // Kembalikan hasil dalam format JSON
        return response()->json([
            'data' => $ujianUser->map(function ($ujian) {
                return [
                    'nama_ujian' => $ujian->ujian->nama_ujian, // Ambil nama ujian dari relasi hasilUjian
                    'hasil' => $ujian->hasilUjian ? [
                        'nilai' => $ujian->hasilUjian->nilai, // Nilai dari tabel hasil_ujian_users
                        'status' => $ujian->hasilUjian->status, // Status dari tabel hasil_ujian_users
                        'waktu_selesai' => $ujian->hasilUjian->waktu_ujian_selesai ? $ujian->hasilUjian->waktu_ujian_selesai->format('d-m-Y H:i:s') : null, // Waktu selesai dalam format yang sesuai
                    ] : null
                ];
            }),
        ]);
    }
}

