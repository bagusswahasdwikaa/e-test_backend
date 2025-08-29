<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignUjianRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang bisa, bisa ditambah policy jika perlu
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => 'Daftar user wajib diisi.',
            'user_ids.array'    => 'Format user_ids tidak valid.',
            'user_ids.*.exists' => 'Salah satu user tidak ditemukan.',
        ];
    }
}
