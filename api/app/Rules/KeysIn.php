<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KeysIn implements ValidationRule
{
    /** @param string[] $allowedKeys */
    public function __construct(private readonly array $allowedKeys) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            return;
        }

        $invalidKeys = array_diff(array_keys($value), $this->allowedKeys);

        if (!empty($invalidKeys)) {
            $fail("The :attribute contains invalid filter keys: " . implode(', ', $invalidKeys) . '.');
        }
    }
}
