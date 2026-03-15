<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAssessment extends Model
{
    use HasFactory;

    protected $table = 'client_assessment';
    protected $primaryKey = 'client_basic_assessment_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'client_basic_assessment_id',
        'client_service_id',
        'client_id',
        'diagnosis',
        'equipment_school_use',
        'equipment_workplace_use',
        'equipment_home_use',
        'equipment_neighborhood_use',
        'other_equipment_use_place',
        'transfer',
        'old_equipment_fulfillment',
        'old_equipment_goodness',
        'cushion_safety',
        'old_equipment_comment',
        'pressure_sores_location_existence',
        'sensation_disruption',
        'pressure_sores_experience',
        'pressure_sores_existence',
        'open_wound',
        'pressure_sores_level',
        'pressure_sores_duration',
        'pressure_sores_cause',
        'pressure_sores_risk',
        'both_arms',
        'left_arm',
        'right_arm',
        'both_legs',
        'left_leg',
        'right_leg',
        'other_person',
        'pedaling_means_comment',
        'overall_posture',
        'pelvic_posture',
        'posture_comment',
        'pelvic_width',
        'left_upper_limb',
        'right_upper_limb',
        'left_lower_limb',
        'right_lower_limb',
        'buttocks_lower_ribs',
        'buttocks_scapula',
        'seat_width',
        'seat_length',
        'upper_holder',
        'lower_holder',
        'lower_seat',
        'middle_seat',
        'kids_wheelchair',
        'rr_holder_width',
        'rr_holder_length',
        'rr_seat_height',
        'st_holder_width',
        'cushion_type',
        'sp_holder_width',
        'sp_holder_length',
        'sp_seat_height',
        'tilting',
        'reclining',
        'folding',
        'light',
        'active',
        'stroller',
        'sp_description',
        'sp_reason',
        'photo_before',
        'photo_after',
    ];

    protected function casts(): array
    {
        return [
            'pelvic_width' => 'decimal:2',
            'left_upper_limb' => 'decimal:2',
            'right_upper_limb' => 'decimal:2',
            'left_lower_limb' => 'decimal:2',
            'right_lower_limb' => 'decimal:2',
            'buttocks_lower_ribs' => 'decimal:2',
            'buttocks_scapula' => 'decimal:2',
            'seat_width' => 'decimal:2',
            'seat_length' => 'decimal:2',
            'upper_holder' => 'decimal:2',
            'lower_holder' => 'decimal:2',
            'lower_seat' => 'decimal:2',
            'middle_seat' => 'decimal:2',
            'pressure_sores_level' => 'integer',
            'rr_holder_length' => 'integer',
            'rr_seat_height' => 'integer',
            'sp_holder_width' => 'integer',
            'sp_holder_length' => 'integer',
            'sp_seat_height' => 'integer',
        ];
    }

    /**
     * Get the client that owns this assessment.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientBio::class, 'client_id', 'client_id');
    }
}
