<?php

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
            '980319+9570', // centenarian
            '980379-9577', // coordination number
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
                    '19980319-9570',
                    '9803199570',
                ],
                'long-format without separator' => [
                    '199803199570',
                    '9803199570',
                ],
                'short-format with separator' => [
                    '980319-9570',
                    '9803199570',
                ],
                'short-format without separator' => [
                    '9803199570',
                    '9803199570',
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
                    '980319+9570',
                    '980319+9570',
                    '189803199570',
                ],
                'centenarian long format with + separator' => [
                    '18980319+9570',
                    '980319+9570',
                    '189803199570',
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
                    '980319-9570',
                    '19980319-9570',
                    '199803199570',
                ],
                'long format input' => [
                    '199803199570',
                    '19980319-9570',
                    '199803199570',
                ],
            ],
        );

    it('can identify coordination numbers', function (string $ssn, bool $isCoordination) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->isCoordinationNumber())->toBe($isCoordination);
    })
        ->with(
            [
                'regular personal number' => ['980319-9570', false],
                'coordination number (day + 60)' => ['980379-9577', true],
            ],
        );

    it('throws an exception for invalid coordination numbers', function ($ssn) {
        new PersonalNumber($ssn);
    })->throws(IdentityException::class)
        ->with(
            [
                '980392-9570', // day 92 - 60 = 32, invalid
                '980300-9570', // day 0 - invalid
            ],
        );

    it('can get an personal-number in short format with separator', function (string $ssn, string $expectedValue) {
        $personalNumber = new PersonalNumber($ssn);

        expect($personalNumber->shortFormat(true))->toBe($expectedValue);
    })
    ->with(
        [
            'long-format with separator' => [
                '19980319-9570',
                '980319-9570',
            ],
            'long-format without separator' => [
                '199803199570',
                '980319-9570',
            ],
            'short-format with separator' => [
                '980319-9570',
                '980319-9570',
            ],
            'short-format without separator' => [
                '9803199570',
                '980319-9570',
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
                    '19980319-9570',
                    '9803199570',
                ],
                'long-format without separator' => [
                    '199803199570',
                    '9803199570',
                ],
                'short-format with separator' => [
                    '980319-9570',
                    '9803199570',
                ],
                'short-format without separator' => [
                    '9803199570',
                    '9803199570',
                ],
            ],
        );
});
