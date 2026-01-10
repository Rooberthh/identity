# Identity

A PHP package to validate and identify Swedish personal numbers (personnummer) and organization numbers (organisationsnummer).

## Usage

### Auto-detect identity type

```php
use Rooberthh\Identity\Identity;

$identity = Identity::identify('980319-9570');
// Returns PersonalNumber instance

$identity = Identity::identify('556074-7569');
// Returns OrganizationNumber instance
```

### Safe identification (no exceptions)

```php
$identity = Identity::tryIdentify('invalid-number');
// Returns null instead of throwing exception

if ($identity = Identity::tryIdentify($input)) {
    // Valid identity
}
```

### Personal number validation

```php
use Rooberthh\Identity\PersonalNumber;

// Validate without creating object
if (PersonalNumber::isValid('980319-9570')) {
    // Valid personal number
}

// Create instance (throws exception if invalid)
$person = new PersonalNumber('980319-9570');
```

### Personal number formatting

```php
$person = new PersonalNumber('9803199570');

$person->shortFormat();        // "9803199570"
$person->shortFormat(true);    // "980319-9570"
$person->longFormat();         // "199803199570"
$person->longFormat(true);     // "19980319-9570"
```

### Extract metadata from personal number

```php
$person = new PersonalNumber('980319-9570');

$person->getBirthDate();       // DateTimeImmutable: 1998-03-19
$person->getAge();             // int: 27 (calculated from today)
$person->getGender();          // Gender::Male
$person->isMale();             // true
$person->isFemale();           // false
$person->isOfAge(18);          // true
$person->isOfAge(30);          // false
```

### Centenarians (100+ years old)

```php
// The + separator indicates a person born 100+ years ago
$person = new PersonalNumber('980319+9570');

$person->isCentenarian();      // true
$person->getBirthDate();       // DateTimeImmutable: 1898-03-19
$person->shortFormat(true);    // "980319+9570"
```

### Coordination numbers (samordningsnummer)

```php
// Coordination numbers have day + 60
$person = new PersonalNumber('980379-9577');

$person->isCoordinationNumber(); // true
$person->getBirthDate();         // DateTimeImmutable: 1998-03-19
```

### Organization number validation

```php
use Rooberthh\Identity\OrganizationNumber;

if (OrganizationNumber::isValid('556074-7569')) {
    // Valid organization number
}

$org = new OrganizationNumber('5560747569');
$org->shortFormat(true);       // "556074-7569"
```

### Error handling

```php
use Rooberthh\Identity\IdentityException;

try {
    $person = new PersonalNumber('invalid');
} catch (IdentityException $e) {
    // "'invalid' is not valid personal number."
}
```
