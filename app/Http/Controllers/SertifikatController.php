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

            // Pastikan folder ada di storage/app/public/sertifikat
            if (!Storage::disk('public')->exists('sertifikat')) {
                Storage::disk('public')->makeDirectory('sertifikat');
            }

            // Data untuk template
            $data = [
                'nama' => strtoupper(trim($user->first_name . ' ' . $user->last_name)),
                'nilai' => $ujianUser->nilai,
                'tanggal_ujian' => Carbon::parse($ujianUser->created_at)->translatedFormat('d-m-Y'),
                'tanggal_terbit' => Carbon::now()->translatedFormat('d-m-Y'),
                'mentor' => 'Samira Hadid',
                'jabatan_mentor' => 'Mentor Penulisan',
                'ketua' => 'Ketut Susilo',
                'jabatan_ketua' => 'Ketua Organisasi'
            ];

            // Buat PDF dari template dengan konfigurasi A4 Landscape
            $pdf = Pdf::loadView('sertifikat.template', $data)
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('dpi', 150)
                ->setOption('enable-local-file-access', true)
                ->setOption('chroot', [public_path()])
                ->setOption('viewport-size', '1920x1080');

            // Simpan ke storage/app/public/sertifikat
            Storage::disk('public')->put($path, $pdf->output());

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
     * Menampilkan sertifikat (stream inline di browser)
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

            $id = (int) $id;
            if ($id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID sertifikat tidak valid.'
                ], 400);
            }

            $sertifikat = Sertifikat::with(['ujian', 'user'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$sertifikat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sertifikat tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            // Cek apakah file fisik ada
            $filePath = storage_path('app/public/' . $sertifikat->path_file);

            // Jika file tidak ada, regenerate PDF
            if (!file_exists($filePath)) {
                Log::warning('File sertifikat tidak ditemukan, regenerating: ' . $filePath);
                
                // Regenerate PDF
                $ujianUser = UjianUser::where('user_id', $user->id)
                    ->where('ujian_id', $sertifikat->ujian_id)
                    ->first();

                if (!$ujianUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data ujian tidak ditemukan untuk regenerasi sertifikat.'
                    ], 404);
                }

                $pdf = $this->generatePdfFromSertifikat($sertifikat, $ujianUser);
                
                // Simpan file yang baru
                Storage::disk('public')->put($sertifikat->path_file, $pdf->output());
                $filePath = storage_path('app/public/' . $sertifikat->path_file);
            }

            // Stream PDF dengan header yang tepat untuk A4 Landscape
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $sertifikat->nama_sertifikat . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in SertifikatController@show: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menampilkan sertifikat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Download sertifikat (force download)
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

            $sertifikat = Sertifikat::with(['ujian', 'user'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$sertifikat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sertifikat tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            // Cek apakah file fisik ada
            $filePath = storage_path('app/public/' . $sertifikat->path_file);

            // Jika file tidak ada, regenerate PDF
            if (!file_exists($filePath)) {
                Log::warning('File sertifikat tidak ditemukan, regenerating: ' . $filePath);
                
                // Regenerate PDF
                $ujianUser = UjianUser::where('user_id', $user->id)
                    ->where('ujian_id', $sertifikat->ujian_id)
                    ->first();

                if (!$ujianUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data ujian tidak ditemukan untuk regenerasi sertifikat.'
                    ], 404);
                }

                $pdf = $this->generatePdfFromSertifikat($sertifikat, $ujianUser);
                
                // Simpan file yang baru
                Storage::disk('public')->put($sertifikat->path_file, $pdf->output());
                $filePath = storage_path('app/public/' . $sertifikat->path_file);
            }

            // Download PDF dengan header yang tepat
            return response()->download($filePath, $sertifikat->nama_sertifikat, [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in SertifikatController@download: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunduh sertifikat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Helper function untuk generate PDF dari data sertifikat
     */
    private function generatePdfFromSertifikat($sertifikat, $ujianUser)
    {
        $user = $sertifikat->user;

        $data = [
            'nama' => strtoupper(trim($user->first_name . ' ' . $user->last_name)),
            'nilai' => $ujianUser->nilai,
            'tanggal_ujian' => Carbon::parse($ujianUser->created_at)->translatedFormat('d-m-Y'),
            'tanggal_terbit' => Carbon::parse($sertifikat->tanggal_diterbitkan)->translatedFormat('d-m-Y'),
            'mentor' => 'Samira Hadid',
            'jabatan_mentor' => 'Mentor Penulisan',
            'ketua' => 'Ketut Susilo',
            'jabatan_ketua' => 'Ketua Organisasi'
        ];

        // Buat PDF dengan konfigurasi optimal untuk A4 Landscape
        return Pdf::loadView('sertifikat.template', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 150)
            ->setOption('enable-local-file-access', true)
            ->setOption('chroot', [public_path()])
            ->setOption('viewport-size', '1920x1080')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);
    }
}