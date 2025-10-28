<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToModel, WithHeadingRow
{
    /**
     * Membuat user baru dari setiap baris di file Excel
     */
    public function model(array $row)
    {
        // Validasi wajib isi
        if (
            empty($row['first_name']) ||
            empty($row['last_name']) ||
            empty($row['email']) ||
            empty($row['instansi'])
        ) {
            Log::warning('Baris dilewati karena data wajib kosong:', $row);
            return null; // lewati baris ini, tidak disimpan
        }

        // Gunakan password default jika kosong
        $password = !empty($row['password']) ? $row['password'] : '12345678';

        // Catat ke log untuk debugging (opsional)
        Log::info('Importing peserta:', $row);

        return new User([
            'id'         => $row['id'] ?? null, 
            'first_name' => trim($row['first_name']),
            'last_name'  => trim($row['last_name']),
            'email'      => strtolower(trim($row['email'])),
            'password'   => Hash::make($password),
            'status'     => $row['status'] ?? 'aktif' || 'non aktif',
            'role'       => 'user',
            'instansi'   => trim($row['instansi']),
            'bio'        => $row['bio'] ?? null,
            'photo_url'  => $row['photo_url'] ?? null,
        ]);
    }
}
