<?php

namespace App\DTOs;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;

class DTO
{
    /**
     * List of properties that should be ignored when transforming the DTO to an array.
     *
     * @var string[]
     */
    protected array $ignoredProperties = [];

    /**
     * Transforms all public properties of the DTO to an array with snake_case keys.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        // Walk each declared public property so we only export the fields the DTO surfaced.
        foreach ($this->publicPropertyNames() as $property) {
            // Skip properties the caller marked as ignored or that currently hold the None sentinel.
            if ($this->shouldSkip($property)) {
                continue;
            }

            // Emit snake_case keys for compatibility with request payloads, while normalizing nested values recursively
            $payload[Str::snake($property)] = $this->normalizeValue($this->{$property});
        }

        return $payload;
    }

    /**
     * @return string[]
     */
    private function publicPropertyNames(): array
    {
        static $cache = [];

        $class = static::class;

        // Cache reflection work per concrete DTO class so multiple instantiations avoid repeated scans.
        if (!array_key_exists($class, $cache)) {
            $reflection = new ReflectionClass($class);
            $cache[$class] = array_map(
                static fn (ReflectionProperty $property) => $property->getName(),
                $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
            );
        }

        return $cache[$class];
    }

    private function shouldSkip(string $property): bool
    {
        // Explicitly ignored properties are never exported.
        if (in_array($property, $this->ignoredProperties)) {
            return true;
        }

        // Treat None sentinels as "no value" and omit them from the resulting array.
        return is_none($this->{$property});
    }

    private function normalizeValue(mixed $value): mixed
    {
        // Recursively convert nested DTOs to arrays so downstream consumers receive plain data structures.
        if ($value instanceof self) {
            return $value->toArray();
        }

        // Normalize arrays (lists or associative) by recursing on each nested value.
        if (is_array($value)) {
            return array_map(fn ($item) => $this->normalizeValue($item), $value);
        }

        return $value;
    }
}
