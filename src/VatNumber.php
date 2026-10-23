<?php

declare(strict_types=1);

namespace Rooberthh\Identity;

use Rooberthh\Identity\Contracts\IdentityNumberInterface;
use Rooberthh\Identity\Exceptions\IdentityException;

/**
 * Swedish VAT registration number (momsregistreringsnummer).
 *
 * Format: "SE" + 10-digit organization number (or personal number for sole traders) + "01".
 */
final class VatNumber implements IdentityNumberInterface
{
    protected string $normalizedNumber;

    protected OrganizationNumber|PersonalNumber $identityNumber;

    public function __construct(public string $number)
    {
        if (! self::isValid($number)) {
            IdentityException::invalidVatNumber($number);
        }

        $this->normalizedNumber = self::normalize($number);

        $inner = substr($this->normalizedNumber, 2, 10);

        $this->identityNumber = OrganizationNumber::isValid($inner)
            ? new OrganizationNumber($inner)
            : new PersonalNumber($inner);
    }

    public static function fromIdentityNumber(OrganizationNumber|PersonalNumber $identityNumber): self
    {
        return new self('SE' . $identityNumber->shortFormat() . '01');
    }

    public static function isValid(string $number): bool
    {
        $normalized = self::normalize($number);

        if (! preg_match('/^SE\d{10}01$/', $normalized)) {
            return false;
        }

        $inner = substr($normalized, 2, 10);

        return OrganizationNumber::isValid($inner) || PersonalNumber::isValid($inner);
    }

    /**
     * VAT numbers have no separator format, so the separator flag is ignored.
     * @param bool $separator
     */
    public function longFormat(bool $separator = false): string
    {
        return $this->normalizedNumber;
    }

    /**
     * VAT numbers have no separator format, so the separator flag is ignored.
     * @param bool $separator
     */
    public function shortFormat(bool $separator = false): string
    {
        return $this->normalizedNumber;
    }

    public static function normalize(string $number): string
    {
        return strtoupper((string) preg_replace('/[^a-zA-Z0-9]/', '', $number));
    }

    public function getIdentityNumber(): OrganizationNumber|PersonalNumber
    {
        return $this->identityNumber;
    }

    public function isSoleTrader(): bool
    {
        return $this->identityNumber instanceof PersonalNumber;
    }
}
