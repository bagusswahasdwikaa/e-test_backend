<?php

namespace App\Exports;

use App\Models\NilaiPeserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NilaiPesertaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = NilaiPeserta::with(['user', 'ujian'])
            ->whereHas('user', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        return $data->map(function ($item) {
            return [
                'ID Peserta'   => $item->user->id,
                'Nama Lengkap' => $item->user->first_name . ' ' . $item->user->last_name,
                'Tanggal'      => $item->tanggal,
                'Nilai'        => $item->nilai,
                'Nama Ujian'   => $item->ujian->nama_ujian ?? '-',
                'Status'       => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID Peserta',
            'Nama Lengkap',
            'Tanggal',
            'Nilai',
            'Nama Ujian',
            'Status',
        ];
    }
}
