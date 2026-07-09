<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

/**
 * @OA\Schema(
 *     schema="LoginRequestBody",
 *     title="Login request body",
 *     type="object",
 *     @OA\Property(property="email", type="string", format="email", maxLength=255),
 *     @OA\Property(property="password", type="string", format="password", maxLength=255)
 * )
 *
 * @property string $email
 * @property string $password
 */
class LoginRequest extends BaseRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }
}
