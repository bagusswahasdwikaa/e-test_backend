<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UjianSeeder extends Seeder
{
    public function run()
    {
        DB::table('ujians')->insert([
            [
                'nama_ujian' => 'Matematika Dasar',
                'tanggal_mulai' => '2024-08-01',
                'tanggal_akhir' => '2024-08-03',
                'durasi' => 60,
                'jumlah_soal' => 10,
                'nilai' => null,
                'kode_soal' => 'MATH001',
                'status' => 'Non Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_ujian' => 'Bahasa Inggris',
                'tanggal_mulai' => '2024-08-02',
                'tanggal_akhir' => '2024-08-02',
                'durasi' => 45,
                'jumlah_soal' => 8,
                'nilai' => null,
                'kode_soal' => 'ENG001',
                'status' => 'Non Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
