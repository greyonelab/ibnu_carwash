<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Car Wash',
            'email' => 'admin@carwash.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create manager user
        User::create([
            'name' => 'Manager Car Wash',
            'email' => 'manager@carwash.com',
            'password' => Hash::make('password'),
            'role' => 'manager'
        ]);

        $this->call([
            ServiceSeeder::class,
            StaffSeeder::class,
        ]);
    }
}
