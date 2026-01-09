<?php

namespace Rooberthh\Identity;

class IdentityException extends \RuntimeException
{
    /** @throws IdentityException */
    public static function invalidOrganizationNumber(string $number): void
    {
        throw new self("'$number' is not valid organization number.");
    }

    /** @throws IdentityException */
    public static function invalidPersonalNumber(string $number): void
    {
        throw new self("'$number' is not valid personal number.");
    }

    /** @throws IdentityException */
    public static function invalidIdentityNumber(string $number): void
    {
        throw new self("'$number' could not be resolved to a personal or organization number.");
    }
}
