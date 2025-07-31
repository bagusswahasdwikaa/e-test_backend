<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUjianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_ujian'   => 'required|string|max:255',
            'tanggal'      => 'required|date',
            'durasi'       => 'required|integer|min:1',
            'jumlah_soal'  => 'required|integer|min:1',
            'nilai'        => 'nullable|numeric|min:0|max:100',
            'kode_soal'    => 'required|string|max:255',
            'status'       => 'required|in:Aktif,Non Aktif',
        ];
    }
}
