<?php

namespace App\Actions\Organization\Users;

use App\DTOs\Organization\User\CreateOrganizationUserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

readonly class CreateOrganizationUserAction
{
    public function __construct() {
    }

    public function execute(CreateOrganizationUserDTO $data, User $user): User
    {
        return DB::transaction(function () use ($data, $user) {

        });
    }
}
