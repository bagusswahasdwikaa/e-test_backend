<?php

namespace App\Http\Controllers;

use App\Models\UjianUser;
use Illuminate\Http\JsonResponse;
use App\Exports\NilaiPesertaExport;
use Maatwebsite\Excel\Facades\Excel;

class NilaiPesertaController extends Controller
{
    /**
     * Ambil daftar seluruh nilai peserta ujian
     */
    public function index(): JsonResponse
    {
        $nilaiPesertas = UjianUser::with(['user', 'ujian'])
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'nama_lengkap' => $item->user->full_name,
                    'id_ujian' => $item->ujian?->id_ujian,
                    'nama_ujian' => $item->ujian?->nama_ujian,
                    'tanggal' => $item->submitted_at?->format('Y-m-d H:i'),
                    'nilai' => $item->nilai,
                    'status' => $item->status_peserta,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $nilaiPesertas,
        ]);
    }
    // Method export ke Excel
    public function export()
    {
        return Excel::download(new NilaiPesertaExport, 'nilai_peserta.xlsx');
    }

    public function riwayatByUser($id): JsonResponse
    {
        $riwayat = UjianUser::with(['ujian'])
            ->where('user_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'nama_ujian' => $item->ujian?->nama_ujian ?? '-',
                    'nilai' => $item->nilai,
                    'status' => $item->status_peserta ?? 'Belum Dikerjakan',
                    'waktu_selesai' => $item->submitted_at?->format('d-m-Y H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $riwayat,
        ]);
    }
}
