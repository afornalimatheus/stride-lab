<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = Organization::first();

        $superAdminUser = User::where('name', 'Super Admin')->first();
        $organization->members()->create([
            'user_id' => $superAdminUser->id,
            'role_id' => Role::where('name', 'SUPER_ADMIN')->first()->id,
        ]);

        $ownerUser = User::where('name', 'Owner')->first();
        $organization->members()->create([
            'user_id' => $ownerUser->id,
            'role_id' => Role::where('name', 'OWNER')->first()->id,
        ]);

        $coachUser = User::where('name', 'Coach')->first();
        $organization->members()->create([
            'user_id' => $coachUser->id,
            'role_id' => Role::where('name', 'COACH')->first()->id,
        ]);

        $athleteUser = User::where('name', 'Athlete')->first();
        $organization->members()->create([
            'user_id' => $athleteUser->id,
            'role_id' => Role::where('name', 'ATHLETE')->first()->id,
        ]);
    }
}
