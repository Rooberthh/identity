<?php

namespace Rooberthh\Identity;

class Identity
{
    public static function identify(string $number): IdentityNumberInterface
    {
        $number = str_replace('-', '', $number);

        if (strlen($number) > 10) {
            $object = new PersonalNumber($number);
        } elseif (strlen($number) === 10 && (int) substr($number, 2, 2) >= 20) {
            $object = new OrganizationNumber($number);
        } else {
            $object = new PersonalNumber($number);
        }

        if (! $object::isValid($number)) {
            IdentityException::invalidIdentityNumber($number);
        }

        return $object;
    }
}
