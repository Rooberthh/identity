<?php

namespace Rooberthh\Identity;

class PersonalNumber implements IdentityNumberInterface
{
    protected string $normalizedNumber;

    public function __construct(public string $number)
    {
        if (! self::isValid($number)) {
            IdentityException::invalidPersonalNumber($number);
        }

        $this->normalizedNumber = self::normalize($number);
    }

    public static function isValid(string $number): bool
    {
        $numberInDigits = self::normalize($number);

        if (strlen($numberInDigits) !== 10) {
            return false;
        }

        $yy = (int) substr($numberInDigits, 0, 2);
        $mm = (int) substr($numberInDigits, 2, 2);
        $dd = (int) substr($numberInDigits, 4, 2);

        // Reject invalid day ranges
        if ($dd < 1 || $dd > 31) {
            return false;
        }

        // Resolve century
        $currentYear = (int) date('Y');
        $century = intdiv($currentYear, 100) * 100;

        if (str_contains($numberInDigits, '+')) {
            $century -= 100;
        }

        $year = $century + $yy;

        if ($year > $currentYear) {
            $year -= 100;
        }


        if (! checkdate($mm, $dd, $year)) {
            return false;
        }

        return Luhn::check($numberInDigits);
    }

    public function longFormat(bool $separator = false): string
    {
        $fullSsn = $this->getYear($this->normalizedNumber) . substr($this->normalizedNumber, 0, 2);

        if ($separator) {
            return substr_replace($fullSsn, '-', -4, 0);
        }

        return $fullSsn;
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

    protected function getYear(string $normalizedNumber): string
    {
        $yy = (int) substr($normalizedNumber, 0, 2);

        // Resolve century
        $currentYear = (int) date('Y');
        $century = intdiv($currentYear, 100) * 100;

        if (str_contains($normalizedNumber, '+')) {
            $century -= 100;
        }

        $year = $century + $yy;

        if ($year > $currentYear) {
            $year -= 100;
        }

        return (string) $year;
    }
}
