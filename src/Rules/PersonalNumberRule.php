<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Rooberthh\Identity\PersonalNumber;

final class PersonalNumberRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! PersonalNumber::isValid($value)) {
            $fail('validation.personal_number')->translate();
        }
    }
}
