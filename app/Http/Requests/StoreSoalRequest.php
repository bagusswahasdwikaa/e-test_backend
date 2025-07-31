<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ujian_id'      => 'required|exists:ujians,id_ujian',
            'pertanyaan'    => 'required|string',
            'media_path'    => 'nullable|string', // bisa disesuaikan validasi file upload jika perlu
            'media_type'    => 'nullable|in:image,video,none',
            'jawabans'      => 'required|array|min:1',
            'jawabans.*.jawaban' => 'required|string',
            'jawabans.*.is_correct' => 'required|boolean',
        ];
    }
}
