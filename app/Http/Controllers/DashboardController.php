<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ujian;
use App\Models\UjianUser;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Jumlah Peserta
        |--------------------------------------------------------------------------
        */
        $pesertaQuery = User::where('role', 'user')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $aktif = 0;
        $tidakAktif = 0;

        foreach ($pesertaQuery as $status => $count) {
            if (strtolower(trim($status)) === 'aktif') {
                $aktif += $count;
            } else {
                $tidakAktif += $count;
            }
        }

        $jumlahPeserta = [
            'aktif' => $aktif,
            'tidak_aktif' => $tidakAktif,
            'total' => $aktif + $tidakAktif,
        ];


        /*
        |--------------------------------------------------------------------------
        | 2. Jumlah Ujian (Selesai diganti “Tidak Aktif”)
        |--------------------------------------------------------------------------
        | Status dari accessor getStatusAttribute() : 
        | - 'Aktif'
        | - 'Belum Dimulai'
        | - 'Non Aktif'
        | - 'Selesai'
        |--------------------------------------------------------------------------
        */
        $ujianAktif = 0;
        $ujianBelumDimulai = 0;
        $ujianTidakAktif = 0;

        $ujians = Ujian::all();

        foreach ($ujians as $ujian) {
            switch ($ujian->status) {
                case 'Aktif':
                    $ujianAktif++;
                    break;

                case 'Belum Dimulai':
                    $ujianBelumDimulai++;
                    break;

                // Semua status lain dianggap tidak aktif
                case 'Non Aktif':
                case 'Selesai':
                default:
                    $ujianTidakAktif++;
                    break;
            }
        }

        $jumlahUjian = [
            'aktif' => $ujianAktif,
            'belum_dimulai' => $ujianBelumDimulai,
            'tidak_aktif' => $ujianTidakAktif,
            'total' => $ujians->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | 3. Rata-rata Nilai per Ujian
        |--------------------------------------------------------------------------
        */
        $rataRataNilai = [];

        foreach ($ujians as $ujian) {
            $peserta = UjianUser::where('ujian_id', $ujian->id_ujian);

            $sudahMengerjakan = (clone $peserta)->where('is_submitted', true)->count();
            $belumMengerjakan = (clone $peserta)->where('is_submitted', false)->count();

            $nilaiRata = (clone $peserta)
                ->where('is_submitted', true)
                ->avg('nilai');

            $rataRataNilai[] = [
                'id_ujian' => $ujian->id_ujian,
                'nama_ujian' => $ujian->nama_ujian,
                'status_ujian' => $ujian->status,
                'rata_rata_nilai' => round($nilaiRata ?? 0, 2),
                'jumlah_peserta' => $peserta->count(),
                'sudah_mengerjakan' => $sudahMengerjakan,
                'belum_mengerjakan' => $belumMengerjakan,
            ];
        }

        return response()->json([
            'peserta' => $jumlahPeserta,
            'ujian' => $jumlahUjian,
            'rata_rata_nilai' => $rataRataNilai,
        ]);
    }
}
