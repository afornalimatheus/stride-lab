<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminUser = User::where('name', 'Super Admin')->first();
        $superAdminUser->roles()->attach(Role::where('name', 'SUPER_ADMIN')->first()->id);

        $ownerUser = User::where('name', 'Owner')->first();
        $ownerUser->roles()->attach(Role::where('name', 'OWNER')->first()->id);

        $coachUser = User::where('name', 'Coach')->first();
        $coachUser->roles()->attach(Role::where('name', 'COACH')->first()->id);

        $athleteUser = User::where('name', 'Athlete')->first();
        $athleteUser->roles()->attach(Role::where('name', 'ATHLETE')->first()->id);
    }
}
