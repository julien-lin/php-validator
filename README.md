# PHP Validator

[🇫🇷 Read in French](README.fr.md) | [🇬🇧 Read in English](README.md)

## 💝 Support the project

If this package is useful to you, consider [becoming a sponsor](https://github.com/sponsors/julien-lin) to support the development and maintenance of this open source project.

---

A modern and advanced validation system for PHP 8+ with custom rules, multilingual messages, conditional validation, and sanitization.

## 🚀 Installation

```bash
composer require julienlinard/php-validator
```

**Requirements**: PHP 8.0 or higher

## ⚡ Quick Start

### Basic Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Validator\Validator;

$validator = new Validator();

$data = [
    'email' => 'user@example.com',
    'password' => 'password123',
    'age' => 25,
];

$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
    'age' => 'required|numeric|min:18',
];

$result = $validator->validate($data, $rules);

if ($result->isValid()) {
    $validated = $result->getValidated();
    // Use validated data
} else {
    $errors = $result->getErrors();
    // Handle errors
}
```

## 📋 Features

- ✅ **Built-in Rules**: 30+ rules including required, email, min, max, numeric, url, in, pattern, date, boolean, between, file, image, size, alpha, alpha_num, alpha_dash, confirmed, ip, ipv4, ipv6, json, uuid, accepted, filled, before, after, different, same
- ✅ **Custom Rules**: Easy to create and register custom validation rules
- ✅ **Multilingual Messages**: Support for custom error messages
- ✅ **Sanitization**: Automatic HTML escaping and trimming
- ✅ **Conditional Validation**: Skip validation for empty values (except required)
- ✅ **Flexible Rules**: String format (`required|email|min:5`) or array format
- ✅ **Validation Result**: Rich result object with error handling

## 📖 Documentation

### Available Rules

#### Required

Validates that a field is not empty.

```php
$rules = ['name' => 'required'];
```

#### Email

Validates that a field contains a valid email address.

```php
$rules = ['email' => 'email'];
```

#### Min / Max

Validates the minimum/maximum length of a string or value.

```php
$rules = [
    'password' => 'min:8',
    'title' => 'max:100',
];
```

#### Numeric

Validates that a value is numeric.

```php
$rules = ['age' => 'numeric'];
```

#### URL

Validates that a field contains a valid URL.

```php
$rules = ['website' => 'url'];
```

#### In

Validates that a value is in a list of allowed values.

```php
$rules = ['status' => 'in:active,inactive,pending'];
```

#### Pattern

Validates that a value matches a regex pattern.

```php
$rules = ['phone' => 'pattern:/^\+?[1-9]\d{1,14}$/'];
```

#### Date

Validates that a field contains a valid date.

```php
$rules = [
    'birthday' => 'date',                    // Any valid date format
    'created_at' => 'date:Y-m-d H:i:s',     // Specific format
];
```

#### Boolean

Validates that a field is a boolean value. Accepts: true, false, 1, 0, "1", "0", "true", "false", "yes", "no", "on", "off".

```php
$rules = ['is_active' => 'boolean'];
```

#### Between

Validates that a value is between two numbers (for numeric) or has a length between two values (for strings).

```php
$rules = [
    'age' => 'between:18,65',        // Numeric: between 18 and 65
    'title' => 'between:5,100',      // String length: between 5 and 100 characters
];
```

#### File

Validates that a field is a valid uploaded file.

```php
$rules = [
    'document' => 'file',           // Any file
    'document' => 'file:10485760',  // Max 10MB (in bytes)
];

// For MIME type validation, use array format:
$rules = [
    'document' => [
        'file' => [10485760, ['application/pdf', 'application/msword']]
    ]
];
```

#### Image

Validates that a field is a valid image file. Automatically checks MIME type and uses `getimagesize()` to ensure it's a real image.

```php
$rules = [
    'avatar' => 'image',           // Any image
    'avatar' => 'image:10485760',  // Max 10MB (in bytes)
];

// For specific image types, use array format:
$rules = [
    'avatar' => [
        'image' => [10485760, ['image/jpeg', 'image/png']]  // Max size first, then allowed types
    ]
];
```

#### Size

Validates that a field has an exact size (for files: bytes, for strings: characters, for numbers: exact value).

```php
$rules = [
    'code' => 'size:6',          // String: exactly 6 characters
    'file' => 'size:1024',        // File: exactly 1024 bytes
    'count' => 'size:10',        // Number: exactly 10
];
```

#### Alpha

Validates that a field contains only letters (including accented characters).

```php
$rules = ['name' => 'alpha'];
```

#### Alpha Num

Validates that a field contains only letters and numbers.

```php
$rules = ['username' => 'alpha_num'];
```

#### Alpha Dash

Validates that a field contains only letters, numbers, dashes and underscores.

```php
$rules = ['slug' => 'alpha_dash'];
```

#### Confirmed

Validates that a field has a matching confirmation field (e.g., `password_confirmation`).

```php
$rules = ['password' => 'required|confirmed'];
// Requires 'password_confirmation' field to match 'password'
```

#### IP Address

Validates that a field is a valid IP address.

```php
$rules = [
    'ip' => 'ip',        // IPv4 or IPv6
    'ip' => 'ipv4',      // IPv4 only
    'ip' => 'ipv6',      // IPv6 only
];
```

#### JSON

Validates that a field contains a valid JSON string.

```php
$rules = ['config' => 'json'];
```

#### UUID

Validates that a field is a valid UUID (v1-v5).

```php
$rules = ['id' => 'uuid'];
```

#### Accepted

Validates that a field is accepted (yes, on, 1, true). Useful for checkboxes and terms acceptance.

```php
$rules = ['terms' => 'accepted'];
```

#### Filled

Validates that a field has a value when present (different from required - allows null if field is not present).

```php
$rules = ['optional_field' => 'filled'];
```

#### Before / After

Validates that a date field is before or after another date.

```php
$rules = [
    'start_date' => 'date|before:end_date',
    'end_date' => 'date|after:start_date',
    'birthday' => 'date|before:today',  // or 'before:2024-01-01'
];
```

#### Different / Same

Validates that a field is different from or same as another field.

```php
$rules = [
    'new_password' => 'different:old_password',
    'password_confirmation' => 'same:password',
];
```

### Multilingual Support

The validator supports multiple languages (French, English, Spanish) out of the box.

```php
// Create validator with a specific locale
$validator = new Validator('en'); // English
$validator = new Validator('fr'); // French (default)
$validator = new Validator('es'); // Spanish

// Or change locale after creation
$validator = new Validator();
$validator->setLocale('en');

// Get current locale
$locale = $validator->getLocale(); // Returns 'en', 'fr', or 'es'
```

**Supported locales:**
- `fr` - French (default)
- `en` - English
- `es` - Spanish

### Custom Messages

You can customize error messages for specific fields and rules.

```php
$validator = new Validator();
$validator->setCustomMessages([
    'email.email' => 'Please provide a valid email address',
    'password.min' => 'Password must be at least :min characters',
    'name.required' => 'The name field is required',
]);
```

### Sanitization

By default, the validator automatically sanitizes input data (trims strings, escapes HTML).

```php
$validator = new Validator();
$validator->setSanitize(true); // Default: true

$data = ['name' => '  <script>alert("xss")</script>  '];
$result = $validator->validate($data, ['name' => 'required']);

// The validated value will be sanitized
$validated = $result->getValidatedValue('name');
// Result: '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'
```

### Custom Rules

You can create and register custom validation rules.

```php
use JulienLinard\Validator\Rules\AbstractRule;
use JulienLinard\Validator\Validator;

class CustomRule extends AbstractRule
{
    public function getName(): string
    {
        return 'custom';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        // Your validation logic
        return $value === 'expected';
    }

    protected function getDefaultMessage(): string
    {
        return 'The :field field is invalid.';
    }
}

$validator = new Validator();
$validator->registerRule(new CustomRule());

$rules = ['field' => 'custom'];
```

### Integration with core-php

This package integrates seamlessly with `core-php` Forms.

```php
use JulienLinard\Core\Controller\Controller;
use JulienLinard\Core\Form\FormResult;
use JulienLinard\Validator\Validator;

class UserController extends Controller
{
    public function store()
    {
        $validator = new Validator();
        $result = $validator->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (!$result->isValid()) {
            $formResult = new FormResult();
            foreach ($result->getErrors() as $field => $errors) {
                foreach ($errors as $error) {
                    $formResult->addError(new FormError($error, $field));
                }
            }
            return $this->view('users/create', ['formResult' => $formResult]);
        }

        // Use validated data
        $validated = $result->getValidated();
        // ...
    }
}
```

## 📚 API Reference

### Validator

#### `validate(array $data, array $rules): ValidationResult`

Validates data against rules.

```php
$result = $validator->validate($data, $rules);
```

#### `setCustomMessages(array $messages): self`

Sets custom error messages.

```php
$validator->setCustomMessages([
    'email.email' => 'Invalid email',
]);
```

#### `setSanitize(bool $sanitize): self`

Enables or disables automatic sanitization.

```php
$validator->setSanitize(false);
```

#### `registerRule(RuleInterface $rule): self`

Registers a custom validation rule.

```php
$validator->registerRule(new CustomRule());
```

### ValidationResult

#### `isValid(): bool`

Checks if validation passed.

```php
if ($result->isValid()) {
    // Success
}
```

#### `hasErrors(): bool`

Checks if validation failed.

```php
if ($result->hasErrors()) {
    // Has errors
}
```

#### `getErrors(): array`

Gets all errors grouped by field.

```php
$errors = $result->getErrors();
// ['email' => ['Email is required'], 'password' => ['Password too short']]
```

#### `getFieldErrors(string $field): array`

Gets errors for a specific field.

```php
$emailErrors = $result->getFieldErrors('email');
```

#### `getFirstError(string $field): ?string`

Gets the first error for a field.

```php
$firstError = $result->getFirstError('email');
```

#### `getValidated(): array`

Gets all validated and sanitized data.

```php
$validated = $result->getValidated();
```

#### `getValidatedValue(string $field, mixed $default = null): mixed`

Gets a validated value for a specific field.

```php
$email = $result->getValidatedValue('email');
```

## 📝 License

MIT License - See the LICENSE file for more details.

## 🤝 Contributing

Contributions are welcome! Feel free to open an issue or a pull request.

## 💝 Support

If this package is useful to you, consider [becoming a sponsor](https://github.com/sponsors/julien-lin) to support the development and maintenance of this open source project.

---

**Developed with ❤️ by Julien Linard**

