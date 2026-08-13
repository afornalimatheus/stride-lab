<?php

namespace App\Http\Requests\Organization\Users;

use App\Enums\RoleEnum;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\Traits\Filterable;
use Illuminate\Validation\Rule;

class IndexOrganizationUserRequest extends PaginationRequest
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
                'role' => [Rule::in(RoleEnum::values())],
                'active' => ['in:0,1'],
            ]),
        );
    }
}
