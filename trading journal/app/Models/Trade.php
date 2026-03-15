<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    /** @use HasFactory<\Database\Factories\TradeFactory> */
    use HasFactory;

    protected $fillable = [
        'pair',
        'type',
        'entry_price',
        'exit_price',
        'size',
        'pnl',
        'status',
        'notes',
        'trade_date',
    ];

    protected $casts = [
        'trade_date' => 'datetime',
        'pnl' => 'decimal:8',
        'entry_price' => 'decimal:8',
        'exit_price' => 'decimal:8',
        'size' => 'decimal:8',
    ];
}
