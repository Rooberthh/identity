# Identity

A PHP package to validate and identify Swedish personal numbers (personnummer) and organization numbers (organisationsnummer).

## Usage

### Auto-detect identity type

```php
use Rooberthh\Identity\Identity;

$identity = Identity::identify('770604-0016');
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
if (PersonalNumber::isValid('770604-0016')) {
    // Valid personal number
}

// Create instance (throws exception if invalid)
$person = new PersonalNumber('770604-0016');
```

### Personal number formatting

```php
$person = new PersonalNumber('7706040016');

$person->shortFormat();        // "7706040016"
$person->shortFormat(true);    // "770604-0016"
$person->longFormat();         // "197706040016"
$person->longFormat(true);     // "19770604-0016"
```

### Extract metadata from personal number

```php
$person = new PersonalNumber('770604-0016');

$person->getBirthDate();       // DateTimeImmutable: 1977-06-04
$person->getAge();             // int (calculated from today)
$person->getGender();          // Gender::Male
$person->isMale();             // true
$person->isFemale();           // false
$person->isOfAge(18);          // true
```

### Centenarians (100+ years old)

```php
// The + separator indicates a person born 100+ years ago
$person = new PersonalNumber('770604+0016');

$person->isCentenarian();      // true
$person->getBirthDate();       // DateTimeImmutable: 1877-06-04
$person->shortFormat(true);    // "770604+0016"
```

### Coordination numbers (samordningsnummer)

```php
// Coordination numbers have day + 60
$person = new PersonalNumber('770664-0005');

$person->isCoordinationNumber(); // true
$person->getBirthDate();         // DateTimeImmutable: 1977-06-04
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
use Rooberthh\Identity\Exceptions\IdentityException;

try {
    $person = new PersonalNumber('invalid');
} catch (IdentityException $e) {
    // "'invalid' is not valid personal number."
}
```
