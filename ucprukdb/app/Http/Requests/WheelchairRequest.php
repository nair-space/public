<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WheelchairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|string|exists:client_bio,client_id',
            'jenis_kursiroda' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Klien wajib dipilih.',
            'client_id.exists' => 'Klien tidak ditemukan.',
            'jenis_kursiroda.required' => 'Jenis kursi roda wajib diisi.',
        ];
    }
}
