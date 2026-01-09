<?php

namespace Rooberthh\Identity;

class OrganizationNumber implements IdentityNumberInterface
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
        $numberInDigits = preg_replace('/\D/', '', $number);

        if ((int) substr($numberInDigits, 2, 2) < 20) {
            return false;
        }

        return Luhn::check($numberInDigits);
    }

    public function longFormat(bool $separator): string
    {
        return $this->shortFormat($separator);
    }

    public function shortFormat(bool $separator): string
    {
        if ($separator) {
            return substr_replace($this->normalizedNumber, '-', -4, 0);
        }

        return $this->normalizedNumber;
    }

    public static function normalize(string $number): string
    {
        return preg_replace('/\D/', '', $number);
    }
}
