# Création de Règles Personnalisées

## Vue d'ensemble

Le validateur `php-validator` permet de créer des règles personnalisées pour répondre à des besoins spécifiques. Il existe deux approches : implémenter `RuleInterface` directement ou étendre `AbstractRule`.

## Approche recommandée : AbstractRule

L'approche la plus simple est d'étendre `AbstractRule`, qui gère automatiquement la traduction et le remplacement des placeholders.

### Structure de base

```php
<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use JulienLinard\Validator\Rules\AbstractRule;

class CustomRule extends AbstractRule
{
    /**
     * Retourne le nom de la règle
     */
    public function getName(): string
    {
        return 'custom';
    }

    /**
     * Valide une valeur
     *
     * @param mixed $value Valeur à valider
     * @param array $params Paramètres de la règle
     * @return bool True si la validation réussit
     */
    public function validate(mixed $value, array $params = []): bool
    {
        // Logique de validation
        return true; // ou false
    }

    /**
     * Retourne le message d'erreur par défaut
     */
    protected function getDefaultMessage(): string
    {
        return 'Le champ :field ne respecte pas la règle personnalisée.';
    }
}
```

### Exemple : Règle "phone"

```php
<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use JulienLinard\Validator\Rules\AbstractRule;

class PhoneRule extends AbstractRule
{
    public function getName(): string
    {
        return 'phone';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Optionnel par défaut
        }

        if (!is_string($value)) {
            return false;
        }

        // Format français : 10 chiffres, optionnellement avec espaces ou tirets
        $pattern = '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/';
        
        return (bool)preg_match($pattern, $value);
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être un numéro de téléphone valide.';
    }
}
```

**Utilisation** :
```php
$validator->registerRule(new PhoneRule());

$data = ['phone' => '06 12 34 56 78'];
$result = $validator->validate($data, ['phone' => 'phone']);
// Passe
```

### Exemple : Règle avec paramètres

```php
<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use JulienLinard\Validator\Rules\AbstractRule;

class MinAgeRule extends AbstractRule
{
    public function getName(): string
    {
        return 'min_age';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        // Paramètre : âge minimum requis
        $minAge = (int)($params[0] ?? 18);

        try {
            $birthDate = new \DateTime($value);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
            
            return $age >= $minAge;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit correspondre à une personne d\'au moins :min_age ans.';
    }
}
```

**Utilisation** :
```php
$validator->registerRule(new MinAgeRule());

$data = ['birthday' => '2010-01-01'];
$result = $validator->validate($data, ['birthday' => 'min_age:18']);
// Échoue si la personne a moins de 18 ans
```

### Exemple : Règle avec accès aux autres champs

Si votre règle nécessite l'accès aux autres champs, vous devez la gérer dans le `Validator` ou créer une règle spéciale.

```php
<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use JulienLinard\Validator\Rules\AbstractRule;

class MatchesFieldRule extends AbstractRule
{
    public function getName(): string
    {
        return 'matches';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        // Cette règle doit être gérée dans Validator car elle nécessite
        // l'accès aux autres champs. On retourne true ici.
        return true;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit correspondre à :other.';
    }
}
```

Puis, dans votre `Validator` personnalisé, gérer cette règle spécialement :

```php
// Dans Validator::validate()
if ($ruleName === 'matches' && !empty($ruleParams) && isset($ruleParams[0])) {
    $otherField = $ruleParams[0];
    if (!isset($data[$otherField]) || $data[$otherField] !== $value) {
        $message = $this->getErrorMessage($field, $ruleName, ['other' => $otherField], $rule);
        $result->addError($field, $message);
    }
    continue;
}
```

## Approche alternative : RuleInterface

Si vous préférez implémenter directement `RuleInterface`, vous devez gérer vous-même la traduction :

```php
<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use JulienLinard\Validator\Rules\RuleInterface;

class CustomRule implements RuleInterface
{
    public function validate(mixed $value, array $params = []): bool
    {
        // Logique de validation
        return true;
    }

    public function getMessage(string $field, array $params = []): string
    {
        // Gérer la traduction manuellement
        return "Le champ {$field} ne respecte pas la règle personnalisée.";
    }
}
```

## Bonnes pratiques

### 1. Gérer les valeurs optionnelles

Par défaut, toutes les règles (sauf `required`) sont optionnelles. Si la valeur est `null` ou `''`, retournez `true` :

```php
public function validate(mixed $value, array $params = []): bool
{
    if ($value === null || $value === '') {
        return true; // Optionnel par défaut
    }
    
    // Validation pour les valeurs présentes
    // ...
}
```

### 2. Valider le type

Vérifiez le type de la valeur avant la validation :

```php
public function validate(mixed $value, array $params = []): bool
{
    if ($value === null || $value === '') {
        return true;
    }

    if (!is_string($value)) {
        return false; // Type incorrect
    }

    // Validation spécifique
    // ...
}
```

### 3. Utiliser les placeholders dans les messages

Utilisez les placeholders pour rendre les messages dynamiques :

```php
protected function getDefaultMessage(): string
{
    return 'Le champ :field doit être au moins :min_age ans.';
}
```

Les placeholders seront automatiquement remplacés par `AbstractRule::getMessage()`.

### 4. Messages multilingues

Pour supporter plusieurs langues, ajoutez les traductions dans les fichiers de traduction :

```php
// src/Validator/Translations/fr.php
return [
    // ...
    'phone' => 'Le champ :field doit être un numéro de téléphone valide.',
];

// src/Validator/Translations/en.php
return [
    // ...
    'phone' => 'The :field field must be a valid phone number.',
];
```

### 5. Tests

Créez des tests pour vos règles personnalisées :

```php
class PhoneRuleTest extends TestCase
{
    public function testPhoneRulePassesWithValidPhone(): void
    {
        $validator = new Validator();
        $validator->registerRule(new PhoneRule());
        
        $result = $validator->validate(
            ['phone' => '06 12 34 56 78'],
            ['phone' => 'phone']
        );
        
        $this->assertTrue($result->isValid());
    }

    public function testPhoneRuleFailsWithInvalidPhone(): void
    {
        $validator = new Validator();
        $validator->registerRule(new PhoneRule());
        
        $result = $validator->validate(
            ['phone' => 'invalid'],
            ['phone' => 'phone']
        );
        
        $this->assertFalse($result->isValid());
    }
}
```

## Exemples avancés

### Règle "unique" (exemple conceptuel)

```php
class UniqueRule extends AbstractRule
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getName(): string
    {
        return 'unique';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        // params[0] = table, params[1] = column (optionnel)
        $table = $params[0] ?? null;
        $column = $params[1] ?? 'id';

        if (!$table) {
            return false;
        }

        // Vérifier l'unicité dans la base de données
        $exists = $this->database->query(
            "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?",
            [$value]
        ) > 0;

        return !$exists;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être unique.';
    }
}
```

### Règle "exists" (exemple conceptuel)

```php
class ExistsRule extends AbstractRule
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getName(): string
    {
        return 'exists';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $table = $params[0] ?? null;
        $column = $params[1] ?? 'id';

        if (!$table) {
            return false;
        }

        $exists = $this->database->query(
            "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?",
            [$value]
        ) > 0;

        return $exists;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit exister dans :table.';
    }
}
```

## Enregistrement

Enregistrez vos règles personnalisées avant la validation :

```php
$validator = new Validator();
$validator->registerRule(new PhoneRule());
$validator->registerRule(new MinAgeRule());

$result = $validator->validate($data, $rules);
```

## Règles globales

Pour utiliser une règle personnalisée dans toute l'application, créez un validateur personnalisé :

```php
class AppValidator extends Validator
{
    public function __construct(string $locale = 'fr')
    {
        parent::__construct($locale);
        
        // Enregistrer les règles personnalisées
        $this->registerRule(new PhoneRule());
        $this->registerRule(new MinAgeRule());
    }
}
```

Puis utilisez-le partout :

```php
$validator = new AppValidator();
$result = $validator->validate($data, $rules);
```

