<?php

namespace App\Actions\Organization\Users;

use App\DTOs\Organization\User\CreateOrganizationUserDTO;
use App\Models\OrganizationUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

readonly class CreateOrganizationUserAction
{
    public function execute(CreateOrganizationUserDTO $data, User $loggedUser): User
    {
        $organization = $loggedUser->currentOrganization();

        if (!$organization) {
            throw new \Exception('User does not belong to any organization.');
        }

        $role = Role::where('name', $data->role)->first();

        if (!$role) {
            throw new \Exception('Invalid role.');
        }

        return DB::transaction(function () use ($data, $organization, $role) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => bcrypt($data->password),
            ]);

            $organization->members()->create([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

            return $user;
        });
    }
}