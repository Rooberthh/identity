<?php

namespace Rooberthh\Identity;

interface IdentityNumberInterface
{
    public function __construct(string $number);

    public static function isValid(string $number): bool;
    public static function normalize(string $number): string;

    public function longFormat(bool $separator = false): string;

    public function shortFormat(bool $separator = false): string;

}
