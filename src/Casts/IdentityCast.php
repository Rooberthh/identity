<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Rooberthh\Identity\Contracts\IdentityNumberInterface;
use Rooberthh\Identity\Identity;

/**
 * @implements CastsAttributes<IdentityNumberInterface, string>
 */
final class IdentityCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param Model $model
     * @param string $key
     * @param mixed $value
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?IdentityNumberInterface
    {
        if ($value === null) {
            return null;
        }

        return Identity::tryIdentify((string) $value);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param Model $model
     * @param string $key
     * @param mixed $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof IdentityNumberInterface) {
            return $value->longFormat();
        }

        return (string) $value;
    }
}
