<?php

namespace App\Http\Requests\Traits;

trait Searchable
{
    protected function allowsSearch(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
