<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Rooberthh\Identity\Exceptions\IdentityException;
use Rooberthh\Identity\PersonalNumber;

/**
 * @implements CastsAttributes<PersonalNumber, string>
 */
final class PersonalNumberCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?PersonalNumber
    {
        if ($value === null) {
            return null;
        }

        try {
            return new PersonalNumber((string) $value);
        } catch (IdentityException) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof PersonalNumber) {
            return $value->longFormat();
        }

        return (string) $value;
    }
}
