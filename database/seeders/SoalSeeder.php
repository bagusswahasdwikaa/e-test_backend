<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoalSeeder extends Seeder
{
    public function run()
    {
        DB::table('soals')->insert([
            [
                'ujian_id' => 1,
                'pertanyaan' => 'Berapa hasil dari 5 + 3?',
                'media_path' => null,
                'media_type' => 'none',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ujian_id' => 2,
                'pertanyaan' => 'Choose the correct synonym of “Happy”.',
                'media_path' => null,
                'media_type' => 'none',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
