<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Rooberthh\Identity\OrganizationNumber;
use Rooberthh\Identity\PersonalNumber;

final class IdentityRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! PersonalNumber::isValid($value) && ! OrganizationNumber::isValid($value)) {
            $fail('validation.identity')->translate();
        }
    }
}
