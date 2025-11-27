<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@bookshare.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create a test regular user
        User::firstOrCreate(
            ['email' => 'user@bookshare.com'],
            [
                'name' => 'Utilisateur Test',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin and test users created successfully!');
        $this->command->info('Admin: admin@bookshare.com / admin123');
        $this->command->info('User: user@bookshare.com / user123');
    }
}
