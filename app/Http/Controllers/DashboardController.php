<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ujian;
use App\Models\UjianUser;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Statistik dashboard:
     * 1. Jumlah peserta (aktif / tidak aktif) dengan role = user
     * 2. Jumlah ujian (aktif / tidak aktif, selesai, belum dimulai)
     * 3. Rata-rata nilai tiap ujian
     */
    public function index(): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Jumlah Peserta (role = user) termasuk aktif & tidak aktif
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
        | 2. Jumlah Ujian
        |--------------------------------------------------------------------------
        | Status ujian diambil dari accessor getStatusAttribute() di model Ujian
        */
        $ujianAktif = 0;
        $ujianTidakAktif = 0;
        $ujianBelumDimulai = 0;
        $ujianSelesai = 0;

        $ujians = Ujian::all();
        foreach ($ujians as $ujian) {
            switch ($ujian->status) {
                case 'Aktif':
                    $ujianAktif++;
                    break;
                case 'Belum Dimulai':
                    $ujianBelumDimulai++;
                    break;
                case 'Selesai':
                    $ujianSelesai++;
                    break;
                default:
                    $ujianTidakAktif++;
                    break;
            }
        }

        $jumlahUjian = [
            'aktif' => $ujianAktif,
            'belum_dimulai' => $ujianBelumDimulai,
            'selesai' => $ujianSelesai,
            'non_aktif' => $ujianTidakAktif,
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
