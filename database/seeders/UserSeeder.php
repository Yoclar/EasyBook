<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role' => 'provider',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role' => 'provider',
            ],
            [
                'name' => 'Mark Johnson',
                'email' => 'mark@example.com',
                'password' => bcrypt('password123'),
                'role' => 'customer',
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
