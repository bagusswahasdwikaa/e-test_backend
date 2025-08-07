<?php

namespace App\Http\Controllers;

use App\Models\NilaiPeserta;
use Illuminate\Http\Request;
use App\Exports\NilaiPesertaExport;
use Maatwebsite\Excel\Facades\Excel;

class NilaiPesertaController extends Controller
{
    public function index()
    {
        $data = NilaiPeserta::with(['user', 'ujian'])
            ->whereHas('user', function ($query) {
                $query->where('role', 'user');  // Hanya user dengan role 'user'
            })
            ->get();

        $result = $data->map(function ($item) {
            return [
                'id_peserta' => $item->user->id,
                'nama_lengkap' => $item->user->first_name . ' ' . $item->user->last_name,
                'tanggal' => $item->tanggal,
                'hasil_tes' => $item->nilai,
                'nama_ujian' => $item->ujian->nama_ujian ?? '-',
                'status' => $item->status,
            ];
        });

        return response()->json($result);
    }

    // Method export ke Excel
    public function export()
    {
        return Excel::download(new NilaiPesertaExport, 'nilai_peserta.xlsx');
    }
}
