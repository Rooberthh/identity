<?php

declare(strict_types=1);

namespace Rooberthh\Identity;

use Rooberthh\Identity\Contracts\IdentityNumberInterface;
use Rooberthh\Identity\Enums\Gender;
use Rooberthh\Identity\Exceptions\IdentityException;

final class PersonalNumber implements IdentityNumberInterface
{
    protected string $normalizedNumber;

    protected bool $centenarian = false;

    protected bool $isCoordinationNumber = false;

    public function __construct(public string $number)
    {
        if (! self::isValid($number)) {
            IdentityException::invalidPersonalNumber($number);
        }

        $this->centenarian = str_contains($number, '+');
        $this->normalizedNumber = self::normalize($number);

        $dd = (int) substr($this->normalizedNumber, 4, 2);
        $this->isCoordinationNumber = $dd > 60;
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

        // Check if coordination number (day + 60)
        $isCoordination = $dd > 60;
        $actualDay = $isCoordination ? $dd - 60 : $dd;

        // Reject invalid day ranges
        if ($actualDay < 1 || $actualDay > 31) {
            return false;
        }

        // Resolve century
        $currentYear = (int) date('Y');
        $century = intdiv($currentYear, 100) * 100;

        $year = $century + $yy;

        if ($year > $currentYear) {
            $year -= 100;
        }

        // Centenarian (+) means one more century back
        if (str_contains($number, '+')) {
            $year -= 100;
        }

        if (! checkdate($mm, $actualDay, $year)) {
            return false;
        }

        return Luhn::check($numberInDigits);
    }

    public function longFormat(bool $separator = false): string
    {
        $fullSsn = $this->getYear() . substr($this->normalizedNumber, 2);

        if ($separator) {
            return substr_replace($fullSsn, '-', -4, 0);
        }

        return $fullSsn;
    }

    public function shortFormat(bool $separator = false): string
    {
        if ($separator) {
            $separatorChar = $this->centenarian ? '+' : '-';

            return substr_replace($this->normalizedNumber, $separatorChar, 6, 0);
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

    protected function getYear(): string
    {
        $yy = (int) substr($this->normalizedNumber, 0, 2);

        // Resolve century
        $currentYear = (int) date('Y');
        $century = intdiv($currentYear, 100) * 100;

        $year = $century + $yy;

        if ($year > $currentYear) {
            $year -= 100;
        }

        // Centenarian (+) means one more century back
        if ($this->centenarian) {
            $year -= 100;
        }

        return (string) $year;
    }

    public function isCoordinationNumber(): bool
    {
        return $this->isCoordinationNumber;
    }

    public function isCentenarian(): bool
    {
        return $this->centenarian;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        $year = $this->getYear();
        $month = substr($this->normalizedNumber, 2, 2);
        $day = (int) substr($this->normalizedNumber, 4, 2);

        if ($this->isCoordinationNumber) {
            $day -= 60;
        }

        return new \DateTimeImmutable(sprintf('%s-%s-%02d', $year, $month, $day));
    }

    public function getAge(): int
    {
        $birthDate = $this->getBirthDate();
        $today = new \DateTimeImmutable('today');

        return $birthDate->diff($today)->y;
    }

    public function getGender(): Gender
    {
        $genderDigit = (int) $this->normalizedNumber[8];

        return $genderDigit % 2 === 0 ? Gender::Female : Gender::Male;
    }

    public function isMale(): bool
    {
        return $this->getGender() === Gender::Male;
    }

    public function isFemale(): bool
    {
        return $this->getGender() === Gender::Female;
    }

    public function isOfAge(int $minimumAge): bool
    {
        return $this->getAge() >= $minimumAge;
    }
}
