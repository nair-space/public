<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'string', 'max:255'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'nomor_telfon' => ['required', 'string', 'max:16'],
            'status' => ['required', 'in:aktif,tidak aktif'],
        ];

        // Password required only on create
        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email' => 'Format email tidak valid.',
        ];
    }
}
