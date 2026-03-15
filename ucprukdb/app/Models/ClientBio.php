<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class ClientBio extends Model
{
    use HasFactory;

    protected $table = 'client_bio';
    protected $primaryKey = 'client_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'client_id',
        'tanggal_daftar',
        'source_id',
        'nik',
        'nama_depan',
        'nama_belakang',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'dusun',
        'kecamatan',
        'kelurahan',
        'provinsi',
        'status_asuransi',
        'nama_asuransi',
        'nomor_asuransi',
        'status_bpjs',
        'status_difabel',
        'dari_klinik',
        'jenis_disabilitas',
        'status_aktivitas',
        'jenis_sekolah',
        'ada_foto',
        'salinan_kk',
        'salinan_ktp',
        'salinan_tagihanlistrik',
        'salinan_slipgaji',
        'info_tambahan',
        'photo_path',
    ];

    /**
     * Get the URL for the client's photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'date',
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Encrypt NIK when setting (PII protection).
     */
    public function setNikAttribute(string $value): void
    {
        $this->attributes['nik'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt NIK when getting.
     */
    public function getNikAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // Return raw if not encrypted (for legacy data)
        }
    }

    /**
     * Get assessments for this client.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(ClientAssessment::class, 'client_id', 'client_id');
    }

    /**
     * Get wheelchairs for this client.
     */
    public function wheelchairs(): HasMany
    {
        return $this->hasMany(WheelchairClient::class, 'client_id', 'client_id');
    }
}
