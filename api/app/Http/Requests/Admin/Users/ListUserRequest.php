<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\RoleEnum;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\Traits\Filterable;
use Illuminate\Validation\Rule;

class ListUserRequest extends PaginationRequest
{
    use Filterable;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            $this->allowedFilters([
                'name',
                'organization',
                'role' => [Rule::in(RoleEnum::values())],
                'active' => ['in:0,1'],
            ]),
        );
    }
}
