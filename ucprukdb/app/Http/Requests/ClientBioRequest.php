<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientBioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_daftar' => ['required', 'date'],
            'source_id' => ['required', 'string', 'max:10'],
            'nik' => ['required', 'string', 'max:20'],
            'nama_depan' => ['required', 'string', 'max:255'],
            'nama_belakang' => ['required', 'string', 'max:255'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nama_panggilan' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'dusun' => ['required', 'string', 'max:255'],
            'kecamatan' => ['required', 'string', 'max:255'],
            'kelurahan' => ['required', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'max:255'],
            'status_asuransi' => ['required', 'in:ya,tidak'],
            'nama_asuransi' => ['nullable', 'string', 'max:255'],
            'nomor_asuransi' => ['nullable', 'string', 'max:30'],
            'status_bpjs' => ['required', 'in:ya,tidak'],
            'status_difabel' => ['required', 'in:ya,tidak'],
            'dari_klinik' => ['nullable', 'string', 'max:255'],
            'jenis_disabilitas' => ['required', 'string', 'max:255'],
            'status_aktivitas' => ['required', 'string', 'max:255'],
            'jenis_sekolah' => ['nullable', 'string', 'max:255'],
            'ada_foto' => ['required', 'in:ya,tidak'],
            'salinan_kk' => ['required', 'in:ya,tidak'],
            'salinan_ktp' => ['required', 'in:ya,tidak'],
            'salinan_tagihanlistrik' => ['required', 'in:ya,tidak'],
            'salinan_slipgaji' => ['required', 'in:ya,tidak'],
            'info_tambahan' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB for upload, will be compressed later
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'in' => ':attribute tidak valid.',
        ];
    }
}
