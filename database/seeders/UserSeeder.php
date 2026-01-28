<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin->value,
        ]);

        User::factory()->create([
            'name' => 'Developer 1',
            'email' => 'developer1@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Developer->value,
        ]);

        User::factory()->create([
            'name' => 'Developer 2',
            'email' => 'developer2@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Developer->value,
        ]);

        User::factory()->create([
            'name' => 'Developer 3',
            'email' => 'developer3@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Developer->value,
        ]);
    }
}
