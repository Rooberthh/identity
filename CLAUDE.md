# Identity Package

PHP library for identifying and validating Swedish personal numbers (personnummer) and organization numbers (organisationsnummer).

## Quick Reference

```bash
# Run tests
./vendor/bin/pest

# Code style (Laravel Pint)
composer pint

# Static analysis (PHPStan)
composer stan
```

## Project Structure

```
src/
  Identity.php              # Entry point - identifies number type from string
  PersonalNumber.php        # Swedish personal number validation/formatting
  OrganizationNumber.php    # Swedish organization number validation/formatting
  IdentityNumberInterface.php # Common interface for identity types
  Luhn.php                  # Luhn algorithm for checksum validation
  IdentityException.php     # Exception handling
tests/
  IdentityTest.php          # Pest test suite
```

## Architecture

- `Identity::identify($number)` is the main entry point - it auto-detects the number type
- Organization numbers have month digits >= 20 (third and fourth digits)
- Personal numbers use standard date validation + Luhn checksum
- Both types implement `IdentityNumberInterface` with `longFormat()` and `shortFormat()` methods

## Code Style

- PSR-4 autoloading under `Rooberthh\Identity` namespace
- Laravel Pint for formatting (pint.json config)
- PHPStan for static analysis
- Pest for testing
