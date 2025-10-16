<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreUjianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_ujian'     => 'required|string|max:255',
            'tanggal_mulai'  => 'required|date_format:Y-m-d H:i:s',
            'tanggal_akhir'  => 'required|date_format:Y-m-d H:i:s|after_or_equal:tanggal_mulai',
            'durasi'         => 'required|integer|min:1',
            'jumlah_soal'    => 'required|integer|min:1',
            'kode_soal'      => [
                'required',
                'string',
                'max:50',
                Rule::unique('ujians', 'kode_soal'),
            ],
            'nilai'                  => 'nullable|integer|min:0|max:100',
            'jenis_ujian'           => 'required|in:PRETEST,POSTEST',
            'standar_minimal_nilai' => 'nullable|integer|min:0|max:100', // dikontrol manual di bawah
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $jenis = $this->input('jenis_ujian');
            $standar = $this->input('standar_minimal_nilai');

            if ($jenis === 'POSTEST' && is_null($standar)) {
                $validator->errors()->add('standar_minimal_nilai', 'Standar minimal nilai wajib diisi untuk POSTEST.');
            }

            if ($jenis === 'PRETEST' && !is_null($standar)) {
                $validator->errors()->add('standar_minimal_nilai', 'Standar minimal nilai tidak boleh diisi untuk PRETEST.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'nama_ujian.required'         => 'Nama ujian wajib diisi.',
            'tanggal_mulai.required'      => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date_format'   => 'Format tanggal mulai harus Y-m-d H:i:s (24 jam).',
            'tanggal_akhir.required'      => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.date_format'   => 'Format tanggal akhir harus Y-m-d H:i:s (24 jam).',
            'tanggal_akhir.after_or_equal'=> 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
            'durasi.required'             => 'Durasi wajib diisi.',
            'durasi.integer'              => 'Durasi harus berupa angka menit.',
            'jumlah_soal.required'        => 'Jumlah soal wajib diisi.',
            'jumlah_soal.integer'         => 'Jumlah soal harus berupa angka.',
            'kode_soal.required'          => 'Kode soal wajib diisi.',
            'kode_soal.unique'            => 'Kode soal sudah digunakan.',
            'nilai.integer'               => 'Nilai harus berupa angka.',
            'nilai.min'                   => 'Nilai minimal 0.',
            'nilai.max'                   => 'Nilai maksimal 100.',
            'jenis_ujian.required'        => 'Jenis ujian wajib dipilih.',
            'jenis_ujian.in'              => 'Jenis ujian harus PRETEST atau POSTEST.',
            'standar_minimal_nilai.integer' => 'Standar minimal nilai harus berupa angka.',
        ];
    }
}
