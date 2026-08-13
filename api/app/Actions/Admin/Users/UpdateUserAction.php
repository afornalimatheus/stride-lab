<?php

namespace App\Actions\Admin\Users;

use App\DTOs\Admin\User\UpdateUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

readonly class UpdateUserAction
{
    public function execute(UpdateUserDTO $data, string $userId): User
    {
        $user = User::findOrFail($userId);

        return DB::transaction(function () use ($data, $user) {
            if ($data->name !== null) {
                $user->name = $data->name;
            }

            if ($data->email !== null) {
                $user->email = $data->email;
            }

            if ($data->password !== null) {
                $user->password = bcrypt($data->password);
            }

            $user->save();

            return $user;
        });
    }
}
