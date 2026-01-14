<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Rooberthh\Identity\OrganizationNumber;

final class OrganizationNumberRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! OrganizationNumber::isValid($value)) {
            $fail('validation.organization_number')->translate();
        }
    }
}
