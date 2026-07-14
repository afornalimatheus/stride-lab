<?php

namespace App\DTOs\Organization\User;

use App\DTOs\DTO;

class CreateOrganizationUserDTO extends DTO
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
