<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@ptcabot.com',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'HSE Officer',
                'email' => 'hse@ptcabot.com',
                'password' => 'hse12345',
                'role' => 'hse_officer',
            ],
            [
                'name' => 'Supervisor',
                'email' => 'supervisor@ptcabot.com',
                'password' => 'supervisor123',
                'role' => 'supervisor',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
