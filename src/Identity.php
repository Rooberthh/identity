<?php

namespace Rooberthh\Identity;

class Identity
{
    public static function identify(string $number): IdentityNumberInterface
    {
        $numberWithoutSeparator = str_replace(['-', '+'], '', $number);

        if (strlen($numberWithoutSeparator) > 10) {
            $object = new PersonalNumber($number);
        } elseif (strlen($numberWithoutSeparator) === 10 && (int) substr($numberWithoutSeparator, 2, 2) >= 20) {
            $object = new OrganizationNumber($number);
        } else {
            $object = new PersonalNumber($number);
        }

        if (! $object::isValid($number)) {
            IdentityException::invalidIdentityNumber($number);
        }

        return $object;
    }

    public static function tryIdentify(string $number): ?IdentityNumberInterface
    {
        try {
            return self::identify($number);
        } catch (IdentityException) {
            return null;
        }
    }
}
