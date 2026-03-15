<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WheelchairClient extends Model
{
    use HasFactory;

    protected $table = 'wheelchair_client';
    protected $primaryKey = 'kursiroda_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kursiroda_id',
        'jenis_kursiroda',
        'client_id',
        'nik',
        'nama_lengkap',
    ];

    /**
     * Get the client that owns this wheelchair.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientBio::class, 'client_id', 'client_id');
    }
}
