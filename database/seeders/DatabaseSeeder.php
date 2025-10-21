<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run AdminUserSeeder to ensure admin users exist
        $this->call(AdminUserSeeder::class);

        // Run DummyDataSeeder to populate application data
        // $this->call(DummyDataSeeder::class);

        // Create test user if not exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'), // You can change this to a more secure password
                'email_verified_at' => now(),
            ]);
        }
    }
}
