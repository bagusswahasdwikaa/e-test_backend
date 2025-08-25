<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUjianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_ujian'     => 'sometimes|string|max:255',
            'tanggal_mulai'  => 'sometimes|date_format:Y-m-d H:i:s',
            'tanggal_akhir'  => 'sometimes|date_format:Y-m-d H:i:s|after_or_equal:tanggal_mulai',
            'durasi'         => 'sometimes|integer|min:1',
            'jumlah_soal'    => 'sometimes|integer|min:1',
            'kode_soal'      => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('ujians', 'kode_soal')->ignore($this->route('id'), 'id_ujian'),
            ],
            'nilai'          => 'sometimes|integer|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_mulai.date_format'    => 'Format tanggal mulai harus Y-m-d H:i:s.',
            'tanggal_akhir.date_format'    => 'Format tanggal akhir harus Y-m-d H:i:s.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
            'kode_soal.unique'             => 'Kode soal sudah digunakan oleh ujian lain.',
        ];
    }
}
