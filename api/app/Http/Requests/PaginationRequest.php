<?php

namespace App\Http\Requests;

class PaginationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['integer'],
            'page' => ['integer'],
        ];
    }

    public function getPage(): int
    {
        return (int) $this->query(key: 'page', default: '1');
    }

    public function getLimit(): int
    {
        $defaultPaginationSize = (int) 15;
        $maxPaginationSize = (int) 100;

        $requestedPaginationSize = (int) $this->query(key: 'per_page');

        if (
            $requestedPaginationSize &&
            ($requestedPaginationSize >= 1 && $requestedPaginationSize <= $maxPaginationSize)
        ) {
            return $requestedPaginationSize;
        }

        return $defaultPaginationSize;
    }
}
