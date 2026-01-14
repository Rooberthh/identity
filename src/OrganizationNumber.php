<?php

declare(strict_types=1);

namespace Rooberthh\Identity;

use Rooberthh\Identity\Contracts\IdentityNumberInterface;
use Rooberthh\Identity\Exceptions\IdentityException;

final class OrganizationNumber implements IdentityNumberInterface
{
    protected string $normalizedNumber;

    public function __construct(public string $number)
    {
        if (! self::isValid($number)) {
            IdentityException::invalidOrganizationNumber($number);
        }

        $this->normalizedNumber = self::normalize($number);
    }

    public static function isValid(string $number): bool
    {
        $numberInDigits = self::normalize($number);

        if ((int) substr($numberInDigits, 2, 2) < 20) {
            return false;
        }

        return Luhn::check($numberInDigits);
    }

    public function longFormat(bool $separator = false): string
    {
        return $this->shortFormat($separator);
    }

    public function shortFormat(bool $separator = false): string
    {
        if ($separator) {
            return substr_replace($this->normalizedNumber, '-', -4, 0);
        }

        return $this->normalizedNumber;
    }

    public static function normalize(string $number): string
    {
        $numberInDigits = preg_replace('/\D/', '', $number);

        if (strlen($numberInDigits) === 12) {
            $numberInDigits = substr($numberInDigits, 2);
        }


        return $numberInDigits;
    }
}
