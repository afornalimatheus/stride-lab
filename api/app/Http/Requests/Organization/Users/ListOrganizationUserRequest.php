<?php

namespace App\Http\Requests\Organization\Users;

use App\Enums\ModalityStatus;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\Traits\Filterable;
use App\Http\Requests\Traits\Searchable;
use App\Rules\ValidEnum;
use Illuminate\Validation\Rule;

class ListOrganizationUserRequest extends PaginationRequest
{
    use Filterable;
    use Searchable;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            $this->allowsSearch(),
            $this->allowedFilters([
                'modality_ids',
                'process_ids',
                'start_date' => ['date'],
                'end_date' => [
                    'date',
                    Rule::when($this->filled('filter.start_date'), 'after:filter.start_date'),
                ],
                'workload' => ['integer', 'min:0'],
                'status' => [new ValidEnum(ModalityStatus::class)],
                'member_ids',
            ]),
        );
    }
}
