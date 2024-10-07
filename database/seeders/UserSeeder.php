<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = [
            [
                'name'              => 'Admin',
                'email'             => config('constant.ADMIN_EMAIL'),
                'password'          => Hash::make(config('constant.ADMIN_PASSWORD')),
                'phone'             => '1234567890',
                'city'              => 'New York',
                'headline'          => 'Software Developer',
                'summary'           => 'Passionate about coding.',
                'role'              => config('constant.USER_ROLE.admin'),
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],  // Find by email
                $user
            );
        }
    }
}
