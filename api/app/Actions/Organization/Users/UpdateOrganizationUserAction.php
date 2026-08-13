<?php

namespace App\Actions\Organization\Users;

use App\DTOs\Organization\User\UpdateOrganizationUserDTO;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

readonly class UpdateOrganizationUserAction
{
    public function execute(UpdateOrganizationUserDTO $data, string $userId, User $loggedUser): User
    {
        $organization = $loggedUser->currentOrganization();

        if (! $organization) {
            throw new \Exception('User does not belong to any organization.');
        }

        $targetUser = User::findOrFail($userId);

        $isMember = $organization->users()->where('users.id', $targetUser->id)->exists();

        if (! $isMember) {
            throw new AccessDeniedHttpException('You do not have permission to update this user.');
        }

        return DB::transaction(function () use ($data, $targetUser, $organization) {
            if ($data->name !== null) {
                $targetUser->name = $data->name;
            }

            if ($data->email !== null) {
                $targetUser->email = $data->email;
            }

            if ($data->password !== null) {
                $targetUser->password = bcrypt($data->password);
            }

            $targetUser->save();

            if ($data->role !== null) {
                $role = Role::where('name', $data->role)->firstOrFail();
                $organization->members()
                    ->where('user_id', $targetUser->id)
                    ->update(['role_id' => $role->id]);
            }

            $targetUser->load('globalRole');

            return $targetUser;
        });
    }
}
