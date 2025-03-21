<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '123456789',
                'password' => Hash::make('pass1234'),
                'role' => 'admin',
            ],
            [
                'name' => 'Regular User-1',
                'email' => 'user@example.com',
                'phone' => '234567890',
                'password' => Hash::make('pass1234'),
                'role' => 'customer',
            ],
            [
                'name' => 'Regular User-2',
                'email' => 'user2@example.com',
                'phone' => '234567891',
                'password' => Hash::make('pass1234'),
                'role' => 'customer',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
