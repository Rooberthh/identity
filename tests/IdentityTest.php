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
