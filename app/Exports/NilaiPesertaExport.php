<?php

namespace App\Exports;

use App\Models\UjianUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NilaiPesertaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = UjianUser::with(['user', 'ujian'])
            ->whereHas('user', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        return $data->map(function ($item) {
            return [
                'ID Peserta'   => $item->user->id,
                'Nama Lengkap' => $item->user->full_name ?? '-',
                'Tanggal'      => optional($item->submitted_at)->format('Y-m-d H:i') ?? '-',
                'Nilai'        => $item->nilai ?? '-',
                'Nama Ujian'   => $item->ujian->nama_ujian ?? '-',
                'Status'       => $item->status_peserta ?? '-',
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
