<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSoalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pertanyaan' => 'nullable|string',
            'media_path' => 'nullable|string',
            'media_type' => 'required|in:image,video,none',
        ];
    }
}
