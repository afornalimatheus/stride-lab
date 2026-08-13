<?php

namespace App\Actions\Admin\Users;

use App\DTOs\Admin\User\CreateUserDTO;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
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

            if ($data->role !== null) {
                $role = Role::where('name', $data->role)->firstOrFail();
                UserRole::create([
                    'user_id' => $newUser->id,
                    'role_id' => $role->id,
                ]);
            }

            return $newUser;
        });
    }
}
