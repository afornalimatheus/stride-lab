<?php

namespace App\Actions\Admin\Users;

use App\Models\User;

readonly class DeleteUserAction
{
    public function execute(string $userId): void
    {
        $user = User::findOrFail($userId);
        $user->delete();
    }
}
