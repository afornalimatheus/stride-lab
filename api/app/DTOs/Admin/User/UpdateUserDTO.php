<?php

namespace App\DTOs\Admin\User;

use App\DTOs\DTO;

class UpdateUserDTO extends DTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}
}
