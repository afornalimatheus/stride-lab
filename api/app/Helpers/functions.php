<?php

use App\DTOs\None;
use Illuminate\Database\Eloquent\ModelNotFoundException;

if (!function_exists(function: 'is_none')) {
    function is_none(mixed $value): bool
    {
        return $value instanceof None;
    }
}

/**
 * @param class-string $class
 */
function abort_404(string $class): ModelNotFoundException
{
    throw (new ModelNotFoundException())->setModel(model: $class);
}
