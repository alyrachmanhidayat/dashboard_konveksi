<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'adminadmin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin2'), // You can change this to a more secure password
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }
}
