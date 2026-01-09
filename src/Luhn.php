<?php

namespace Rooberthh\Identity;

class Luhn
{
    public static function check(string $number): bool
    {
        $numberInDigits = preg_replace('/\D/', '', $number);

        if (strlen($numberInDigits) === 12) {
            $numberInDigits = substr($numberInDigits, 2);
        }

        $sum = 0;

        $digits = str_split($numberInDigits);

        foreach ($digits as $key => $number) {
            if ($key % 2 === 0) {
                $number = (int) $number * 2;
            }

            if ($number >= 10) {
                $number = $number - 9;
            }

            $sum += $number;
        }

        return $sum % 10 === 0;
    }
}
