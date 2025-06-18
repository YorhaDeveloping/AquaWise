<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $expertRole = Role::where('name', 'expert')->first();
        $userRole = Role::where('name', 'user')->first();

        // Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@aquawise.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);

        // Expert user
        $expert = User::create([
            'name' => 'Expert User',
            'email' => 'expert@aquawise.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $expert->assignRole($expertRole);

        // Regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@aquawise.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole($userRole);
    }
} 