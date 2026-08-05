<?php

namespace App\Actions\Admin\Users;

use App\DTOs\Admin\User\CreateUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

readonly class CreateUserAction
{
    public function __construct() {}

    public function execute(CreateUserDTO $data, User $user): User
    {
        return DB::transaction(function () use ($data) {
            $newUser = new User;
            $newUser->name = $data->name;
            $newUser->email = $data->email;
            $newUser->password = bcrypt($data->password);
            $newUser->save();

            return $newUser;
        });
    }
}
