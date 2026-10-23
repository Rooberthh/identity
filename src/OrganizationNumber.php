<?php

declare(strict_types=1);

namespace Rooberthh\Identity;

use Rooberthh\Identity\Contracts\IdentityNumberInterface;
use Rooberthh\Identity\Enums\Format;
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

    /**
     * Organization numbers have no century, so short and long formats are identical.
     * @param Format $format
     */
    public function format(Format $format = Format::Long): string
    {
        return match ($format) {
            Format::Short, Format::Long => $this->withoutSeparator(),
            Format::ShortWithSeparator, Format::LongWithSeparator => $this->withSeparator(),
        };
    }

    protected function withoutSeparator(): string
    {
        return $this->normalizedNumber;
    }

    protected function withSeparator(): string
    {
        return substr_replace($this->normalizedNumber, '-', -4, 0);
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
