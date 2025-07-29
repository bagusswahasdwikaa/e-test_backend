<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JawabanSeeder extends Seeder
{
    public function run()
    {
        DB::table('jawabans')->insert([
            // Jawaban soal 1
            ['soal_id' => 1, 'jawaban' => '6', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['soal_id' => 1, 'jawaban' => '8', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['soal_id' => 1, 'jawaban' => '10', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['soal_id' => 1, 'jawaban' => '12', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],

            // Jawaban soal 2
            ['soal_id' => 2, 'jawaban' => 'Angry', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['soal_id' => 2, 'jawaban' => 'Joyful', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['soal_id' => 2, 'jawaban' => 'Cold', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['soal_id' => 2, 'jawaban' => 'Lonely', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
