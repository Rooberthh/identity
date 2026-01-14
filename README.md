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

## Laravel Usage

### Validation Rules

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Rooberthh\Identity\Rules\PersonalNumberRule;
use Rooberthh\Identity\Rules\OrganizationNumberRule;
use Rooberthh\Identity\Rules\IdentityRule;

class StoreCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Validate personal number only
            'personal_number' => ['required', new PersonalNumberRule],

            // Validate organization number only
            'organization_number' => ['required', new OrganizationNumberRule],

            // Validate either type (personal or organization)
            'identity_number' => ['required', new IdentityRule],
        ];
    }
}
```

### Model Casts

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Rooberthh\Identity\Casts\PersonalNumberCast;
use Rooberthh\Identity\Casts\OrganizationNumberCast;
use Rooberthh\Identity\Casts\IdentityCast;

class Customer extends Model
{
    protected function casts(): array
    {
        return [
            'personal_number' => PersonalNumberCast::class,
            'organization_number' => OrganizationNumberCast::class,
            'identity_number' => IdentityCast::class,  // Auto-detects type
        ];
    }
}
```

### Usage with Casted Attributes

```php
$customer = Customer::find(1);

// Personal number - returns PersonalNumber object
$customer->personal_number->shortFormat(true);  // "770604-0016"
$customer->personal_number->longFormat();       // "197706040016"
$customer->personal_number->getBirthDate();     // DateTimeImmutable
$customer->personal_number->getAge();           // 48
$customer->personal_number->getGender();        // Gender::Male
$customer->personal_number->isMale();           // true

// Organization number - returns OrganizationNumber object
$customer->organization_number->shortFormat(true);  // "556074-7569"

// Identity cast - auto-detects and returns appropriate type
if ($customer->identity_number instanceof PersonalNumber) {
    echo $customer->identity_number->getAge();
}

// Setting values - accepts string or object
$customer->personal_number = '770604-0016';
$customer->personal_number = new PersonalNumber('770604-0016');
$customer->save();  // Stored as "197706040016" (long format)
```
