<?php

namespace App\DTOs\Organization\User;

use App\DTOs\DTO;

class UpdateOrganizationUserDTO extends DTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $role = null,
    ) {}
}
