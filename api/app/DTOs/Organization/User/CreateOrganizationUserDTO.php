<?php

namespace App\DTOs\Organization\User;

use App\DTOs\DTO;

class CreateOrganizationUserDTO extends DTO
{
    protected array $ignoredProperties = [];

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ) {}
}
