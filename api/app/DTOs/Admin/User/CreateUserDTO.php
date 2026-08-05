<?php

namespace App\DTOs\Admin\User;

use App\DTOs\DTO;

class CreateUserDTO extends DTO
{
    protected array $ignoredProperties = [];

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $role = null,
    ) {}
}
