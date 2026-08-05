<?php

namespace App\Http\Requests\Traits;

use App\Rules\KeysIn;

trait Filterable
{
    /**
     * @param  string[]|array<string, array<int, mixed>>  $filters
     * @return array<string, array<int, KeysIn|string>>
     */
    private function allowedFilters(array $filters): array
    {
        $parsedFilters = [];

        foreach ($filters as $filter => $rules) {
            $filter = is_int($filter) ? $rules : $filter;
            $rules = is_array($rules) ? $rules : [];
            $parsedFilters[$filter] = array_merge(['string'], $rules);
        }

        $baseRules = [
            'filter' => ['array', new KeysIn(array_keys($parsedFilters))],
        ];

        foreach ($parsedFilters as $key => $value) {
            $baseRules["filter.{$key}"] = $value;
        }

        return $baseRules;
    }
}
