<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CloneUjianRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk melakukan request ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Tentukan logika otorisasi jika diperlukan
    }

    /**
     * Aturan validasi untuk request ini.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'kode_soal' => [
                'required',          // Kode soal wajib diisi
                'string',            // Kode soal harus berupa string
                'max:50',            // Maksimal panjang kode soal adalah 50 karakter
                Rule::unique('ujians', 'kode_soal')->ignore($this->route('id'), 'id_ujian'), // Validasi kode soal unik kecuali untuk ujian yang sedang dikloning
            ],
            'tanggal_mulai'  => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:today', // Tanggal mulai bisa disesuaikan (optional)
            'tanggal_akhir'  => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:tanggal_mulai', // Tanggal akhir bisa disesuaikan, harus setelah atau sama dengan tanggal mulai
        ];
    }

    /**
     * Pesan kesalahan untuk validasi.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'kode_soal.required'          => 'Kode soal wajib diisi.',
            'kode_soal.string'            => 'Kode soal harus berupa teks.',
            'kode_soal.max'               => 'Kode soal maksimal 50 karakter.',
            'kode_soal.unique'            => 'Kode soal sudah digunakan, harap pilih kode soal lain.',
            'tanggal_mulai.date_format'   => 'Format tanggal mulai harus Y-m-d H:i:s (24 jam).',
            'tanggal_mulai.after_or_equal'=> 'Tanggal mulai harus setelah atau sama dengan hari ini.',
            'tanggal_akhir.date_format'   => 'Format tanggal akhir harus Y-m-d H:i:s (24 jam).',
            'tanggal_akhir.after_or_equal'=> 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
