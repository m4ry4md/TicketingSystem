<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Custom Eloquent cast for handling UUID attributes.
 * Ensures that a UUID is automatically generated if no value is provided when setting the attribute.
 */
class Uuid implements CastsAttributes
{
    /**
     * Cast the given value when retrieving the attribute from the database.
     * In this case, no transformation is needed, return the value as is.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model The Eloquent model instance.
     * @param  string  $key The name of the attribute being cast.
     * @param  mixed  $value The raw value retrieved from the database.
     * @param  array<string, mixed>  $attributes All raw attributes from the model.
     * @return string|null The casted value (UUID string or null).
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Return the retrieved value directly without modification.
        return $value;
    }

    /**
     * Prepare the given value for storage in the database.
     * If the provided value is empty or null, generate a new UUID.
     * Otherwise, use the provided value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model The Eloquent model instance.
     * @param  string  $key The name of the attribute being cast.
     * @param  mixed  $value The value being set on the model.
     * @param  array<string, mixed>  $attributes All raw attributes currently set on the model.
     * @return string The value prepared for storage (ensured to be a UUID string if initially empty).
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        // If the incoming value is falsy (null, empty string, etc.), generate a new UUID string.
        // Otherwise, return the provided value.
        return $value ?: Str::uuid()->toString();
    }
}
