<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Rooberthh\Identity\VatNumber;

final class VatNumberRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! VatNumber::isValid($value)) {
            $fail('validation.vat_number')->translate();
        }
    }
}
