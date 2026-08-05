<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\RoleEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CreateUserRequest",
 *     title="Create User Request",
 *     type="object",
 *     required={"name", "email", "password"},
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="User name"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User email"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="User password"
 *     )
 * )
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $role
 */
class CreateUserRequest extends BaseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['nullable', 'string', Rule::in([RoleEnum::OWNER->value, RoleEnum::COACH->value, RoleEnum::ATHLETE->value])],
        ];
    }
}
