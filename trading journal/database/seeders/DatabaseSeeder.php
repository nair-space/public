<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin Trader',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // Guest user (Read-only)
        User::factory()->create([
            'name' => 'Guest Viewer',
            'email' => 'guest@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        \App\Models\Trade::factory(20)->create();
    }
}
