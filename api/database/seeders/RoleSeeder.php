<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create([
            'id' => 1,
            'name' => RoleEnum::SUPER_ADMIN->value,
        ]);

        Role::create([
            'id' => 2,
            'name' => RoleEnum::OWNER->value,
        ]);

        Role::create([
            'id' => 3,
            'name' => RoleEnum::MANAGER->value,
        ]);

        Role::create([
            'id' => 4,
            'name' => RoleEnum::COACH->value,
        ]);

        Role::create([
            'id' => 5,
            'name' => RoleEnum::ATHLETE->value,
        ]);
    }
}
