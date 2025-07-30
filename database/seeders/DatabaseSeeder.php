<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            UjianSeeder::class,
            SoalSeeder::class,
            JawabanSeeder::class,
            DaftarNilaiPesertaSeeder::class,
        ]);
    }
}
