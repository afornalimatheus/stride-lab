<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Coach',
            'email' => 'coach@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Athlete',
            'email' => 'athlete@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
