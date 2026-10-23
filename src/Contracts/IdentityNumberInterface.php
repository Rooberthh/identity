<?php

declare(strict_types=1);

namespace Rooberthh\Identity\Contracts;

use Rooberthh\Identity\Enums\Format;

interface IdentityNumberInterface
{
    public function __construct(string $number);

    public static function isValid(string $number): bool;
    public static function normalize(string $number): string;

    public function format(Format $format = Format::Long): string;
}
