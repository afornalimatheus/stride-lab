<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;
use Illuminate\Http\Resources\MissingValue;

class JsonResource extends BaseJsonResource
{
    /**
     * @var array<string, mixed>
     */
    private array $attributes;

    public function __construct($resource)
    {
        parent::__construct($resource);

        if (! ($this->resource instanceof Model)) {
            return;
        }

        $this->attributes = $this->resource->toArray();
    }

    /**
     * Intercept property access to automatically check if attribute was selected and return formatted values.
     *
     * @param  string  $key
     */
    public function __get($key): mixed
    {
        if (isset($this->attributes)) {
            if (array_key_exists($key, $this->attributes)) {
                return $this->attributes[$key];
            }

            if ($this->resource instanceof Model) {
                return new MissingValue;
            }
        }

        return parent::__get($key);
    }
}
