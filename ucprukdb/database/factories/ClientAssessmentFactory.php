<?php

namespace Database\Factories;

use App\Models\ClientAssessment;
use App\Models\ClientBio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientAssessmentFactory extends Factory
{
    protected $model = ClientAssessment::class;

    public function definition(): array
    {
        return [
            'client_basic_assessment_id' => 'ASM' . $this->faker->unique()->numerify('#######'),
            'client_service_id' => 'SRV' . $this->faker->numerify('#######'),
            'client_id' => ClientBio::factory(),
            'diagnosis' => $this->faker->sentence(),
            'equipment_school_use' => $this->faker->word(),
            'equipment_workplace_use' => $this->faker->word(),
            'equipment_home_use' => $this->faker->word(),
            'equipment_neighborhood_use' => $this->faker->word(),
            'other_equipment_use_place' => $this->faker->word(),
            'transfer' => $this->faker->randomElement(['Mandiri', 'Bantuan']),
            'old_equipment_fulfillment' => $this->faker->word(),
            'old_equipment_goodness' => $this->faker->word(),
            'cushion_safety' => $this->faker->randomElement(['Aman', 'Berisiko']),
            'old_equipment_comment' => $this->faker->sentence(),
            'pressure_sores_location_existence' => 'Tidak ada',
            'sensation_disruption' => 'Normal',
            'pressure_sores_experience' => 'Tidak',
            'pressure_sores_existence' => 'Tidak',
            'open_wound' => 'tidak',
            'pressure_sores_level' => 0,
            'pressure_sores_duration' => '0',
            'pressure_sores_cause' => '-',
            'pressure_sores_risk' => 'tidak',
            'both_arms' => 'ya',
            'left_arm' => 'ya',
            'right_arm' => 'ya',
            'both_legs' => 'tidak',
            'left_leg' => 'tidak',
            'right_leg' => 'tidak',
            'other_person' => 'tidak',
            'pedaling_means_comment' => $this->faker->sentence(),
            'overall_posture' => 'Normal',
            'pelvic_posture' => 'Simetris',
            'posture_comment' => $this->faker->sentence(),
            'pelvic_width' => $this->faker->numberBetween(20, 50),
            'left_upper_limb' => $this->faker->numberBetween(30, 60),
            'right_upper_limb' => $this->faker->numberBetween(30, 60),
            'left_lower_limb' => $this->faker->numberBetween(30, 80),
            'right_lower_limb' => $this->faker->numberBetween(30, 80),
            'buttocks_lower_ribs' => $this->faker->numberBetween(15, 30),
            'buttocks_scapula' => $this->faker->numberBetween(25, 45),
            'seat_width' => $this->faker->numberBetween(25, 45),
            'seat_length' => $this->faker->numberBetween(30, 50),
            'upper_holder' => $this->faker->numberBetween(10, 25),
            'lower_holder' => $this->faker->numberBetween(10, 25),
            'lower_seat' => $this->faker->numberBetween(5, 15),
            'middle_seat' => $this->faker->numberBetween(5, 15),
            'kids_wheelchair' => $this->faker->randomElement(['ya', 'tidak']),
            'rr_holder_width' => 'Normal',
            'rr_holder_length' => $this->faker->numberBetween(30, 50),
            'rr_seat_height' => $this->faker->numberBetween(40, 60),
            'st_holder_width' => 'Normal',
            'cushion_type' => 'Foam',
            'sp_holder_width' => $this->faker->numberBetween(30, 50),
            'sp_holder_length' => $this->faker->numberBetween(30, 50),
            'sp_seat_height' => $this->faker->numberBetween(40, 60),
            'tilting' => 'tidak',
            'reclining' => 'tidak',
            'folding' => 'ya',
            'light' => 'ya',
            'active' => 'ya',
            'stroller' => 'tidak',
            'sp_description' => $this->faker->text(),
            'sp_reason' => $this->faker->sentence(),
            'photo_before' => null,
            'photo_after' => null,
        ];
    }
}
