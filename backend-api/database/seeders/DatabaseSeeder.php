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
        // User::factory(10)->create();

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

        $this->call([
            TicketSeeder::class,
            CommentSeeder::class,
            ChatSeeder::class,
        ]);
    }
}
