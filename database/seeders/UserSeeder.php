<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Panasonic',
            'email' => 'P4nasonicadmin@gmail.com',
            'password' => Hash::make('adminhr123'),
            'role' => 'admin',
            'instansi' => 'HRD',
            'status' => 'aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);

        User::create([
            'id' => 2,
            'first_name' => 'Admin',
            'last_name' => 'IT',
            'email' => 'P4nasonicit@gmail.com',
            'password' => Hash::make('adminit123'),
            'role' => 'admin',
            'instansi' => 'IT',
            'status'=> 'aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);

        User::create([
            'id' => 3,
            'first_name' => 'Thomas Alfa',
            'last_name' => 'Edi Sound',
            'email' => 'thomashoreg@gmail.com',
            'password' => Hash::make('P4nasonic'),
            'role' => 'user',
            'instansi' => 'QA',
            'status'=> 'aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);
    }
}
