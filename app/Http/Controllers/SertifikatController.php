<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Models\Ujian;
use App\Models\UjianUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SertifikatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Menampilkan semua sertifikat user yang login
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ], 401);
            }

            $sertifikats = Sertifikat::with('ujian')
                ->where('user_id', $user->id)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sertifikats
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in SertifikatController@index: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data sertifikat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate sertifikat setelah user lulus POSTEST
     */
    public static function generateSertifikat($ujianUser)
    {
        try {
            $user = $ujianUser->user ?? Auth::user();
            $ujian = $ujianUser->ujian;

            if (!$ujian || $ujian->jenis_ujian !== 'POSTEST') {
                return null; // hanya untuk ujian POSTEST
            }

            // Cegah duplikasi
            $existing = Sertifikat::where('user_id', $user->id)
                ->where('ujian_id', $ujian->id_ujian)
                ->first();

            if ($existing) {
                return $existing;
            }

            // Generate PDF Sertifikat
            $namaFile = 'sertifikat_' . $user->id . '_' . $ujian->id_ujian . '_' . time() . '.pdf';
            $path = 'sertifikat/' . $namaFile;

            // Pastikan folder ada di storage/public/sertifikat
            if (!Storage::exists('public/sertifikat')) {
                Storage::makeDirectory('public/sertifikat');
            }

            // Buat PDF dari template
            $pdf = Pdf::loadView('sertifikat.template', [
                'nama' => trim($user->first_name . ' ' . $user->last_name),
                'nama_ujian' => $ujian->nama_ujian,
                'tanggal' => Carbon::now()->translatedFormat('d F Y'),
                'nilai' => $ujianUser->nilai,
            ])
            ->setOption('isHtml5ParserEnabled', true) // Enable HTML5 parsing
            ->setOption('isRemoteEnabled', true) // Enable remote resources
            ->setOption('defaultFont', 'DejaVu Sans') // Font default yang support Unicode
            ->setOption('dpi', 300); // Resolusi PDF (semakin tinggi semakin tajam)


            // Simpan ke folder storage/app/public/sertifikat
            Storage::put('public/' . $path, $pdf->output());

            // Simpan ke database
            $sertifikat = Sertifikat::create([
                'user_id' => $user->id,
                'ujian_id' => $ujian->id_ujian,
                'nama_sertifikat' => $namaFile,
                'path_file' => $path,
                'tanggal_diterbitkan' => now(),
            ]);

            return $sertifikat;
        } catch (\Exception $e) {
            Log::error('Error in generateSertifikat: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Menampilkan / mengunduh sertifikat
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ], 401);
            }

            $sertifikat = Sertifikat::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$sertifikat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sertifikat tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            /**
             * Struktur penyimpanan di server:
             * public/storage/public/sertifikat/namafile.pdf
             * jadi file_path = public_path('storage/public/sertifikat/namafile.pdf')
             */
            $filePath = public_path('storage/public/' . $sertifikat->path_file);

            if (!file_exists($filePath)) {
                Log::error('File sertifikat tidak ditemukan di path: ' . $filePath);

                return response()->json([
                    'success' => false,
                    'message' => 'File sertifikat tidak ditemukan di server.'
                ], 404);
            }

            // Menyajikan file PDF agar bisa langsung dibuka atau diunduh
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $sertifikat->nama_sertifikat . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in SertifikatController@show: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunduh sertifikat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Fungsi download eksplisit (opsional)
     */
    public function download($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ], 401);
            }

            $sertifikat = Sertifikat::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$sertifikat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sertifikat tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            $filePath = public_path('storage/public/' . $sertifikat->path_file);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File sertifikat tidak ditemukan di server.'
                ], 404);
            }

            return response()->download($filePath, $sertifikat->nama_sertifikat, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            Log::error('Error in SertifikatController@download: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunduh file sertifikat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
