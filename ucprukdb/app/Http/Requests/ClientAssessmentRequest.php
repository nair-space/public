<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|string|exists:client_bio,client_id',
            'client_service_id' => 'nullable|string|max:50',
            'diagnosis' => 'nullable|string|max:255',
            'equipment_school_use' => 'nullable|string|max:255',
            'equipment_workplace_use' => 'nullable|string|max:255',
            'equipment_home_use' => 'nullable|string|max:255',
            'equipment_neighborhood_use' => 'nullable|string|max:255',
            'other_equipment_use_place' => 'nullable|string|max:255',
            'transfer' => 'nullable|string|max:100',
            'old_equipment_fulfillment' => 'nullable|string|max:255',
            'old_equipment_goodness' => 'nullable|string|max:255',
            'cushion_safety' => 'nullable|string|max:100',
            'old_equipment_comment' => 'nullable|string',
            'pressure_sores_location_existence' => 'nullable|string|max:255',
            'sensation_disruption' => 'nullable|string|max:255',
            'pressure_sores_experience' => 'nullable|string|max:100',
            'pressure_sores_existence' => 'nullable|string|max:100',
            'open_wound' => 'nullable|string|in:ya,tidak',
            'pressure_sores_level' => 'nullable|integer',
            'pressure_sores_duration' => 'nullable|string|max:100',
            'pressure_sores_cause' => 'nullable|string|max:255',
            'pressure_sores_risk' => 'nullable|string|in:ya,tidak',
            'both_arms' => 'nullable|string|in:ya,tidak',
            'left_arm' => 'nullable|string|in:ya,tidak',
            'right_arm' => 'nullable|string|in:ya,tidak',
            'both_legs' => 'nullable|string|in:ya,tidak',
            'left_leg' => 'nullable|string|in:ya,tidak',
            'right_leg' => 'nullable|string|in:ya,tidak',
            'other_person' => 'nullable|string|in:ya,tidak',
            'pedaling_means_comment' => 'nullable|string',
            'overall_posture' => 'nullable|string|max:255',
            'pelvic_posture' => 'nullable|string|max:255',
            'posture_comment' => 'nullable|string',
            'pelvic_width' => 'nullable|numeric',
            'left_upper_limb' => 'nullable|numeric',
            'right_upper_limb' => 'nullable|numeric',
            'left_lower_limb' => 'nullable|numeric',
            'right_lower_limb' => 'nullable|numeric',
            'buttocks_lower_ribs' => 'nullable|numeric',
            'buttocks_scapula' => 'nullable|numeric',
            'seat_width' => 'nullable|numeric',
            'seat_length' => 'nullable|numeric',
            'upper_holder' => 'nullable|numeric',
            'lower_holder' => 'nullable|numeric',
            'lower_seat' => 'nullable|numeric',
            'middle_seat' => 'nullable|numeric',
            'kids_wheelchair' => 'nullable|string|in:ya,tidak',
            'rr_holder_width' => 'nullable|string|max:100',
            'rr_holder_length' => 'nullable|integer',
            'rr_seat_height' => 'nullable|integer',
            'st_holder_width' => 'nullable|string|max:100',
            'cushion_type' => 'nullable|string|max:100',
            'sp_holder_width' => 'nullable|integer',
            'sp_holder_length' => 'nullable|integer',
            'sp_seat_height' => 'nullable|integer',
            'tilting' => 'nullable|string|in:ya,tidak',
            'reclining' => 'nullable|string|in:ya,tidak',
            'folding' => 'nullable|string|in:ya,tidak',
            'light' => 'nullable|string|in:ya,tidak',
            'active' => 'nullable|string|in:ya,tidak',
            'stroller' => 'nullable|string|in:ya,tidak',
            'sp_description' => 'nullable|string',
            'sp_reason' => 'nullable|string',
            'photo_before' => 'nullable|string',
            'photo_after' => 'nullable|string',
        ];
    }
}
