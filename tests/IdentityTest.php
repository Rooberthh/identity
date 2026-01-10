<?php

declare(strict_types=1);

use Rooberthh\Identity\Gender;
use Rooberthh\Identity\Identity;
use Rooberthh\Identity\IdentityException;
use Rooberthh\Identity\OrganizationNumber;
use Rooberthh\Identity\PersonalNumber;
use Rooberthh\Identity\Tests\IdentityTestCase;

uses(IdentityTestCase::class);

it('can determine the identity from a swedish personal number', function ($personalNumber) {
    $identity = Identity::identify($personalNumber);

    expect($identity)->toBeInstanceOf(PersonalNumber::class);
})
    ->with(
        [
            '19681204-3765',
            '681204-3765',
            '196812043765',
            '6812043765',
            '770604+0016', // centenarian
            '770664-0005', // coordination number
        ],
    );

it('can determine the identity from a swedish organizational-number', function ($organizationNumber) {
    $identity = Identity::identify($organizationNumber);

    expect($identity)->toBeInstanceOf(OrganizationNumber::class);
})
    ->with(
        [
            '556074-7569',
            '5560747569',
        ],
    );

it('throws an exception if the identity cannot be resolved', function ($organizationNumber) {
    $identity = Identity::identify($organizationNumber);

    expect($identity)->toBeInstanceOf(OrganizationNumber::class);
})->throws(IdentityException::class)
    ->with(
        [
            '556074-7561',
            '5560747561',
            '20071218-3512',
            '071218-3512',
            '200712183512',
            '0712183512',
        ],
    );

describe('OrganizationNumber', function () {
    it('throws an exception if the organization-number is invalid when creating a organizationNumber object', function ($organizationNumber) {
        new OrganizationNumber($organizationNumber);
    })->throws(IdentityException::class)
        ->with(
            [
                '556074-7561',
                '5560747561',
                '20071218-3512',
                '200712183512',
            ],
        );

    it('can get an organization-number with separator', function (string $organizationNumber, string $expectedValue) {
        $organizationNumber = new OrganizationNumber($organizationNumber);

        expect($organizationNumber->shortFormat(true))->toBe($expectedValue);
    })
        ->with(
            [
                'with separator' => [
                    '556074-7569',
                    '556074-7569',
                ],
                'without separator' => [
                    '5560747569',
                    '556074-7569',
                ],
            ],
        );

    it('can get an personal-number in short format without separator', function (string $ssn, string $expectedValue) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->shortFormat())->toBe($expectedValue);
    })
        ->with(
            [
                'long-format with separator' => [
                    '19770604-0016',
                    '7706040016',
                ],
                'long-format without separator' => [
                    '197706040016',
                    '7706040016',
                ],
                'short-format with separator' => [
                    '770604-0016',
                    '7706040016',
                ],
                'short-format without separator' => [
                    '7706040016',
                    '7706040016',
                ],
            ],
        );
});

describe('PersonalNumber', function () {
    it('throws an exception if the ssn is invalid when creating a personalNumber object', function ($ssn) {
        new PersonalNumber($ssn);
    })->throws(IdentityException::class)
        ->with(
            [
                '556074-7561',
                '5560747561',
                '20071218-3512',
                '071218-3512',
                '200712183512',
                '0712183512',
            ],
        );

    it('can identify centenarians using + separator', function (string $ssn, string $expectedShort, string $expectedLong) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->shortFormat(true))->toBe($expectedShort);
        expect($personalNumber->longFormat())->toBe($expectedLong);
    })
        ->with(
            [
                'centenarian with + separator' => [
                    '770604+0016',
                    '770604+0016',
                    '187706040016',
                ],
                'centenarian long format with + separator' => [
                    '18770604+0016',
                    '770604+0016',
                    '187706040016',
                ],
            ],
        );

    it('can get a personal-number in long format', function (string $ssn, string $expectedWithSeparator, string $expectedWithoutSeparator) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->longFormat(true))->toBe($expectedWithSeparator);
        expect($personalNumber->longFormat(false))->toBe($expectedWithoutSeparator);
    })
        ->with(
            [
                'short format input' => [
                    '770604-0016',
                    '19770604-0016',
                    '197706040016',
                ],
                'long format input' => [
                    '197706040016',
                    '19770604-0016',
                    '197706040016',
                ],
            ],
        );

    it('can identify coordination numbers', function (string $ssn, bool $isCoordination) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->isCoordinationNumber())->toBe($isCoordination);
    })
        ->with(
            [
                'regular personal number' => ['770604-0016', false],
                'coordination number (day + 60)' => ['770664-0005', true],
            ],
        );

    it('throws an exception for invalid coordination numbers', function ($ssn) {
        new PersonalNumber($ssn);
    })->throws(IdentityException::class)
        ->with(
            [
                '770692-0016', // day 92 - 60 = 32, invalid
                '770600-0016', // day 0 - invalid
            ],
        );

    it('can get an personal-number in short format with separator', function (string $ssn, string $expectedValue) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->shortFormat(true))->toBe($expectedValue);
    })
    ->with(
        [
            'long-format with separator' => [
                '19770604-0016',
                '770604-0016',
            ],
            'long-format without separator' => [
                '197706040016',
                '770604-0016',
            ],
            'short-format with separator' => [
                '770604-0016',
                '770604-0016',
            ],
            'short-format without separator' => [
                '7706040016',
                '770604-0016',
            ],
        ],
    );

    it('can get an personal-number in short format without separator', function (string $ssn, string $expectedValue) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->shortFormat())->toBe($expectedValue);
    })
        ->with(
            [
                'long-format with separator' => [
                    '19770604-0016',
                    '7706040016',
                ],
                'long-format without separator' => [
                    '197706040016',
                    '7706040016',
                ],
                'short-format with separator' => [
                    '770604-0016',
                    '7706040016',
                ],
                'short-format without separator' => [
                    '7706040016',
                    '7706040016',
                ],
            ],
        );
});

describe('PersonalNumber metadata', function () {
    it('can get the birth date from a personal number', function (string $ssn, string $expectedDate) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->getBirthDate()->format('Y-m-d'))->toBe($expectedDate);
    })
        ->with(
            [
                'regular personal number' => ['770604-0016', '1977-06-04'],
                'long format' => ['197706040016', '1977-06-04'],
                'centenarian' => ['770604+0016', '1877-06-04'],
                'coordination number' => ['770664-0005', '1977-06-04'],
            ],
        );

    it('can get the gender from a personal number', function () {
        $male = new PersonalNumber('770604-0016');
        $female = new PersonalNumber('770604-6187');

        expect($male->getGender())->toBe(Gender::Male);
        expect($female->getGender())->toBe(Gender::Female);
    });

    it('can check if personal number is male', function () {
        $male = new PersonalNumber('770604-0016');
        $female = new PersonalNumber('770604-6187');

        expect($male->isMale())->toBeTrue();
        expect($male->isFemale())->toBeFalse();
        expect($female->isMale())->toBeFalse();
        expect($female->isFemale())->toBeTrue();
    });

    it('can identify centenarians', function (string $ssn, bool $expectedCentenarian) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->isCentenarian())->toBe($expectedCentenarian);
    })
        ->with(
            [
                'not a centenarian' => ['770604-0016', false],
                'centenarian with +' => ['770604+0016', true],
            ],
        );

    it('can check if a person is of age', function (string $ssn, int $minimumAge, bool $expectedResult) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->isOfAge($minimumAge))->toBe($expectedResult);
    })
        ->with(
            [
                'adult over 18' => ['770604-0016', 18, true],
                'adult over 21' => ['770604-0016', 21, true],
                'centenarian over 100' => ['770604+0016', 100, true],
            ],
        );

    it('can calculate age', function () {
        $personalNumber = new PersonalNumber('770604-0016');

        expect($personalNumber->getAge())->toBeGreaterThanOrEqual(47);
    });
});

describe('Identity::tryIdentify', function () {
    it('returns PersonalNumber for valid personal number', function () {
        $identity = Identity::tryIdentify('770604-0016');

        expect($identity)->toBeInstanceOf(PersonalNumber::class);
    });

    it('returns OrganizationNumber for valid organization number', function () {
        $identity = Identity::tryIdentify('556074-7569');

        expect($identity)->toBeInstanceOf(OrganizationNumber::class);
    });

    it('returns null for invalid input', function ($input) {
        expect(Identity::tryIdentify($input))->toBeNull();
    })
        ->with(
            [
                'invalid checksum' => '556074-7561',
                'invalid personal number' => '20071218-3512',
                'random string' => 'not-a-number',
                'empty string' => '',
            ],
        );
});
