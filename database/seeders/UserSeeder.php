<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Administrator;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Check if admin user already exists
        if (!User::where('username', 'admin')->exists()) {
            // Create admin user
            $user = User::create([
                'username' => 'admin',
                'email' => 'admin@school.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Create admin profile
            Administrator::create([
                'user_id' => $user->user_id,
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '1234567890',
            ]);
            
            $this->command->info('Admin user created successfully');
        } else {
            $this->command->info('Admin user already exists, skipping creation');
        }
    }
}