<?php

namespace App\Http\Controllers;

use App\Models\UjianUser;
use Illuminate\Http\JsonResponse;

class RankController extends Controller
{
    /**
     * Menampilkan ranking peserta berdasarkan nilai dan waktu penyelesaian
     * 
     * @param int $ujianId
     * @return JsonResponse
     */
    public function getRankByUjian($ujianId): JsonResponse
    {
        // Ambil peserta ujian beserta user dan ujian
        $ranking = UjianUser::with(['user', 'ujian'])
            ->where('ujian_id', $ujianId)
            ->whereNotNull('nilai')
            ->orderByDesc('nilai')              // Urutkan berdasarkan nilai tertinggi
            ->orderBy('submitted_at', 'asc')    // Jika nilai sama, yang lebih cepat selesai naik
            ->get()
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'user_id' => $item->user_id,
                    'nama_lengkap' => $item->user->full_name ?? '-',
                    'nilai' => $item->nilai,
                    'status' => $item->status_peserta ?? '-',
                    'waktu_selesai' => $item->submitted_at?->format('d-m-Y H:i:s') ?? '-',
                ];
            });

        // ✅ Jika belum ada nilai peserta
        if ($ranking->isEmpty()) {
            return response()->json([
                'success' => true,
                'ujian_id' => $ujianId,
                'total_peserta' => 0,
                'data' => [],
                'message' => 'Belum ada peserta yang mengikuti atau menyelesaikan ujian ini.',
            ], 200);
        }

        // ✅ Jika ada data ranking
        return response()->json([
            'success' => true,
            'ujian_id' => $ujianId,
            'total_peserta' => $ranking->count(),
            'data' => $ranking,
            'message' => 'Data ranking berhasil diambil.',
        ], 200);
    }
}
