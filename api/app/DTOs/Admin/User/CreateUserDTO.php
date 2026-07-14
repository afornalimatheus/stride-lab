<?php

namespace App\DTOs\Admin\User;

use App\DTOs\DTO;

class CreateUserDTO extends DTO
{
    protected array $ignoredProperties = [];

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
