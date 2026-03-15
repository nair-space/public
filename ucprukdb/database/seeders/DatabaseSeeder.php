<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientBio;
use App\Models\ClientAssessment;
use App\Models\WheelchairClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin & Staff
        User::create([
            'user_id' => 'USR0000001',
            'username' => 'admin',
            'password' => Hash::make('admin123', ['driver' => 'argon2id']),
            'nama_lengkap' => 'Administrator UCPRUK',
            'jabatan' => 'administrator',
            'email' => 'admin@ucpruk.org',
            'nomor_telfon' => '08123456789',
            'status' => 'aktif',
        ]);

        User::create([
            'user_id' => 'USR0000002',
            'username' => 'staff',
            'password' => Hash::make('staff123', ['driver' => 'argon2id']),
            'nama_lengkap' => 'Staff UCPRUK',
            'jabatan' => 'staff',
            'email' => 'staff@ucpruk.org',
            'nomor_telfon' => '08987654321',
            'status' => 'aktif',
        ]);

        // 2. Create Random Users
        User::factory()->count(10)->create();

        // 3. Create Clients with Assessments and Wheelchairs
        ClientBio::factory()
            ->count(50)
            ->create()
            ->each(function ($client) {
                // Each client has 1-2 assessments
                ClientAssessment::factory()
                    ->count(rand(1, 2))
                    ->create([
                        'client_id' => $client->client_id
                    ]);

                // 70% of clients have a wheelchair record
                if (rand(1, 10) <= 7) {
                    WheelchairClient::factory()->create([
                        'client_id' => $client->client_id,
                        'nik' => $client->nik,
                        'nama_lengkap' => $client->nama_lengkap,
                    ]);
                }
            });
    }
}
