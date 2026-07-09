<?php

namespace App\Http\Requests;

use App\DTOs\None;
use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;

class BaseRequest extends FormRequest
{
    /**
     * Cache for parsed property metadata, keyed by class name.
     * Example structure:
     * [
     *   SampleRequest::class => [
     *     'field1' => ['hasNone' => true, 'isFile' => false],
     *     'image'  => ['hasNone' => true, 'isFile' => true],
     *   ],
     * ].
     *
     * @var array<string, array<string, array{hasNone:bool, isFile:bool}>>
     */
    private static array $metaCache = [];

    /**
     * Parse the docblock once per request class and return metadata:
     *   - hasNone: whether the property type includes None
     *   - isFile: whether the property type includes UploadedFile
     *
     * @return array<string, array{hasNone:bool, isFile:bool}>
     */
    private function meta(): array
    {
        // Resolve the actual subclass name (e.g. SampleBaseRequest)
        $class = static::class;

        // If we already parsed this class before, return from cache
        if (isset(self::$metaCache[$class])) {
            return self::$metaCache[$class];
        }

        // Get the PHPDoc comment from the ReflectionClass
        // (contains the @property annotations we need)
        $docs = (new ReflectionClass($this))->getDocComment() ?: '';

        // Prepare regex matches container
        $matches = [];

        // Run regex over the docblock to extract property definitions
        // Each match gives:
        //   [1] = full union of types (e.g. "array<int, UploadedFile>|None")
        //   [2] = property name (e.g. "images")
        preg_match_all(
            pattern: '/@property\s+((?:[\w\\\\]+(?:<[^>]+>)?(?:\[\])?)(?:\|(?:[\w\\\\]+(?:<[^>]+>)?(?:\[\])?))*)\s+\$(\w+)/',
            subject: $docs,
            matches: $matches,
            flags: PREG_SET_ORDER
        );

        // Will hold parsed metadata for each property
        $meta = [];

        // Loop over every @property match
        foreach ($matches as $m) {
            $union = $m[1]; // e.g. "array<int, \Illuminate\Http\UploadedFile>|None"
            $prop = $m[2]; // e.g. "images"

            // Split by union separator "|" into tokens
            $tokens = explode('|', $union);
            $hasNone = false;
            $isFile = false;

            // Inspect each union part
            foreach ($tokens as $t) {
                // Replace generics, array brackets, commas with spaces
                // e.g. "array<int, \App\Thing>" → "array int  \App\Thing "
                $cleaned = str_replace(['<', '>', ',', '[', ']'], ' ', $t);

                // Split cleaned string into smaller tokens
                foreach (preg_split(pattern: '/\s+/', subject: trim($cleaned)) ?: [] as $tok) {
                    if ($tok === '') {
                        continue;
                    }

                    // Remove leading "\" from class names
                    $tok = ltrim($tok, '\\');

                    // Extract short class name
                    // e.g. "\Illuminate\Http\UploadedFile" → "UploadedFile"
                    $short = preg_replace('~^.*\\\\~', '', $tok) ?? $tok;

                    // Flag if this type is None
                    if ($short === 'None') {
                        $hasNone = true;
                    }
                    // Flag if this type is UploadedFile
                    if ($short === 'UploadedFile') {
                        $isFile = true;
                    }

                    // Optimization: if we found both, stop parsing this property
                    if ($hasNone && $isFile) {
                        break 2;
                    }
                }
            }

            // Store results for this property
            $meta[$prop] = ['hasNone' => $hasNone, 'isFile' => $isFile];
        }

        // Cache and return the metadata for this class
        return self::$metaCache[$class] = $meta;
    }

    /**
     * Magic getter for request properties.
     * Uses the metadata parsed from the docblock to decide:
     *   - Return input value ($this->get())
     *   - Return uploaded file ($this->file())
     *   - Return None object if property allows it and value is missing.
     */
    public function __get($key)
    {
        // Look up metadata for the requested property
        // Defaults to ['hasNone' => false, 'isFile' => false] if unknown
        $m = $this->meta()[$key] ?? ['hasNone' => false, 'isFile' => false];

        // If property type allows None, set default = new None()
        $default = $m['hasNone']
            ? new None()
            : null;

        // If property type includes UploadedFile, fetch it from file bag
        if ($m['isFile']) {
            $f = $this->file(key: $key);

            return $f ?? $default;
        }

        // Otherwise fetch from input bag
        return $this->get(key: $key, default: $default);
    }
}
