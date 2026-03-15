<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = 'USR' . str_pad((string) $this->faker->unique()->numberBetween(10, 99999), 7, '0', STR_PAD_LEFT);

        return [
            'user_id' => $id,
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password', ['driver' => 'argon2id']),
            'nama_lengkap' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['administrator', 'staff']),
            'email' => $this->faker->unique()->safeEmail(),
            'nomor_telfon' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['aktif', 'tidak aktif']),
        ];
    }
}
