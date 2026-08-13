<?php

namespace App\Actions\Organization\Users;

use App\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

readonly class DeleteOrganizationUserAction
{
    public function execute(string $userId, User $loggedUser): void
    {
        $organization = $loggedUser->currentOrganization();

        if (!$organization) {
            throw new \Exception('User does not belong to any organization.');
        }

        $targetUser = User::findOrFail($userId);

        $isMember = $organization->users()->where('users.id', $targetUser->id)->exists();

        if (!$isMember) {
            throw new AccessDeniedHttpException('You do not have permission to delete this user.');
        }

        $organization->members()
            ->where('user_id', $targetUser->id)
            ->delete();

        $targetUser->delete();
    }
}
