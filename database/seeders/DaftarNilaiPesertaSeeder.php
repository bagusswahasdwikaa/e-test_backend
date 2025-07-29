<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaftarNilaiPesertaSeeder extends Seeder
{
    public function run()
    {
        DB::table('daftar_nilai_peserta')->insert([
            [
                'user_id' => 1,
                'ujian_id' => 1,
                'tanggal' => '2024-08-01',
                'nilai' => 90,
                'status' => 'Selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'ujian_id' => 2,
                'tanggal' => '2024-08-02',
                'nilai' => null,
                'status' => 'Belum Dikerjakan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
