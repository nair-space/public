<?php

namespace Database\Factories;

use App\Models\WheelchairClient;
use App\Models\ClientBio;
use Illuminate\Database\Eloquent\Factories\Factory;

class WheelchairClientFactory extends Factory
{
    protected $model = WheelchairClient::class;

    public function definition(): array
    {
        return [
            'kursiroda_id' => 'WCH' . $this->faker->unique()->numerify('#######'),
            'jenis_kursiroda' => $this->faker->randomElement(['Active Rigid', 'Folding Standard', 'Rough Rider', 'Motivation Active']),
            'client_id' => ClientBio::factory(),
            'nik' => function (array $attributes) {
                return ClientBio::find($attributes['client_id'])->nik;
            },
            'nama_lengkap' => function (array $attributes) {
                return ClientBio::find($attributes['client_id'])->nama_lengkap;
            },
        ];
    }
}
