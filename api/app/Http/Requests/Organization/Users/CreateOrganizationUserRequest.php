<?php

namespace App\Http\Requests\Organization\Users;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CreateOrganizationUserRequest",
 *     title="Create Organization User Request",
 *     type="object",
 *     required={"name", "email", "password"},
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
 */
class CreateOrganizationUserRequest extends BaseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->where('client_id', $this->getClient()->id)],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
