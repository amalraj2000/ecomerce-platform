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
        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // password is 'password' by default but we can be explicit
            'role' => 'admin',
        ]);

        // Vendor
        $vendorUser = User::factory()->create([
            'name' => 'Vendor User',
            'email' => 'vendor@example.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
        ]);
        
        \App\Models\Vendor::create([
            'user_id' => $vendorUser->id,
            'store_name' => 'KartFlip Retail',
            'slug' => 'kartflip-retail',
            'description' => 'The best store ever.',
            'is_verified' => true,
        ]);

        // Standard User
        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }
}
