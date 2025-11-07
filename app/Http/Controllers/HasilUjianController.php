<?php

namespace App\Http\Controllers;

use App\Models\UjianUser;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HasilUjianController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Mendapatkan user yang sedang login
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ], 401);
            }

            // Ambil data ujian yang sudah dikerjakan oleh user
            $ujianUser = UjianUser::with(['hasilUjian', 'ujian'])
                ->where('user_id', $user->id)
                ->get();

            // Jika tidak ada data hasil ujian
            if ($ujianUser->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Anda belum mengikuti ujian apapun atau hasil ujian belum tersedia.'
                ], 200);
            }

            // Kembalikan hasil dalam format JSON
            $data = $ujianUser->map(function ($ujian) use ($user) {
                // Pastikan relasi ujian ada
                if (!$ujian->ujian) {
                    return null;
                }

                // Cek sertifikat jika POSTEST dan sudah dikerjakan
                $sertifikatId = null;
                if (isset($ujian->ujian->jenis_ujian) && 
                    $ujian->ujian->jenis_ujian === 'POSTEST' && 
                    $ujian->hasilUjian && 
                    $ujian->hasilUjian->status === 'Sudah Dikerjakan') {
                    
                    $sertifikat = Sertifikat::where('user_id', $user->id)
                        ->where('ujian_id', $ujian->ujian->id_ujian)
                        ->first();
                    
                    $sertifikatId = $sertifikat ? $sertifikat->id : null;
                }

                return [
                    'nama_ujian' => $ujian->ujian->nama_ujian ?? 'Tidak Ada Nama',
                    'jenis_ujian' => $ujian->ujian->jenis_ujian ?? 'UNKNOWN',
                    'id_ujian' => $ujian->ujian->id_ujian ?? null,
                    'hasil' => $ujian->hasilUjian ? [
                        'nilai' => $ujian->hasilUjian->nilai ?? 0,
                        'status' => $ujian->hasilUjian->status ?? 'Belum Dikerjakan',
                        'waktu_selesai' => $ujian->hasilUjian->waktu_ujian_selesai ? 
                            $ujian->hasilUjian->waktu_ujian_selesai->format('d-m-Y H:i:s') : null,
                        'sertifikat_id' => $sertifikatId,
                    ] : null
                ];
            })->filter(); // Filter null values

            return response()->json([
                'success' => true,
                'data' => $data->values() // Reset array keys
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in HasilUjianController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data hasil ujian.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}