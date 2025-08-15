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
            'last_name' => 'Pulu',
            'email' => 'adminPulu@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);

        User::create([
            'id' => 2,
            'first_name' => 'User',
            'last_name' => 'Zaki',
            'email' => 'userZak@gmail.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'status'=> 'non aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);

        User::create([
            'id' => 3,
            'first_name' => 'Thomas Alfa',
            'last_name' => 'Edi Sound',
            'email' => 'thomashoreg@gmail.com',
            'password' => Hash::make('P4nasonic'),
            'role' => 'user',
            'status'=> 'aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);

        User::create([
            'id' => 1000,
            'first_name' => 'Rando',
            'last_name' => 'Mustofa',
            'email' => 'lockcaps911@gmail.com',
            'password' => Hash::make('P4nasonic'),
            'role' => 'admin',
            'status'=> 'aktif',
            'api_token' => \Illuminate\Support\Str::random(60),
        ]);
    }
}
