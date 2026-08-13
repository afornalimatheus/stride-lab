<?php

namespace App\Http\Requests\Admin\Users;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name
 * @property string|null $email
 * @property string|null $password
 */
class UpdateUserRequest extends BaseRequest
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
        ];
    }
}
