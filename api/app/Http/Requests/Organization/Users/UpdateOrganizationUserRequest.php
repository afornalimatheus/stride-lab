<?php

namespace App\Http\Requests\Organization\Users;

use App\Enums\RoleEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $role
 */
class UpdateOrganizationUserRequest extends BaseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('userId'))],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', 'string', Rule::in([RoleEnum::OWNER->value, RoleEnum::MANAGER->value, RoleEnum::COACH->value, RoleEnum::ATHLETE->value])],
        ];
    }
}
