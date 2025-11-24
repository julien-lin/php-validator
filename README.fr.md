# PHP Validator

[🇫🇷 Lire en français](README.fr.md) | [🇬🇧 Read in English](README.md)

## 💝 Soutenir le projet

Si ce package vous est utile, envisagez de [devenir un sponsor](https://github.com/sponsors/julien-lin) pour soutenir le développement et la maintenance de ce projet open source.

---

Système de validation avancé pour PHP 8+ avec règles personnalisées, messages multilingues, validation conditionnelle et sanitization.

## 🚀 Installation

```bash
composer require julienlinard/php-validator
```

**Requirements** : PHP 8.0 ou supérieur

## ⚡ Démarrage rapide

### Utilisation de base

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
    // Utiliser les données validées
} else {
    $errors = $result->getErrors();
    // Gérer les erreurs
}
```

## 📋 Fonctionnalités

- ✅ **Règles intégrées** : required, email, min, max, numeric, url, in, pattern
- ✅ **Règles personnalisées** : Facile de créer et enregistrer des règles de validation
- ✅ **Messages multilingues** : Support des messages d'erreur personnalisés
- ✅ **Sanitization** : Échappement HTML et nettoyage automatique
- ✅ **Validation conditionnelle** : Ignorer la validation pour les valeurs vides (sauf required)
- ✅ **Règles flexibles** : Format string (`required|email|min:5`) ou format array
- ✅ **Résultat de validation** : Objet résultat riche avec gestion des erreurs

## 📖 Documentation

### Règles disponibles

#### Required

Valide qu'un champ n'est pas vide.

```php
$rules = ['name' => 'required'];
```

#### Email

Valide qu'un champ contient une adresse email valide.

```php
$rules = ['email' => 'email'];
```

#### Min / Max

Valide la longueur minimale/maximale d'une chaîne ou d'une valeur.

```php
$rules = [
    'password' => 'min:8',
    'title' => 'max:100',
];
```

#### Numeric

Valide qu'une valeur est numérique.

```php
$rules = ['age' => 'numeric'];
```

#### URL

Valide qu'un champ contient une URL valide.

```php
$rules = ['website' => 'url'];
```

#### In

Valide qu'une valeur est dans une liste de valeurs autorisées.

```php
$rules = ['status' => 'in:active,inactive,pending'];
```

#### Pattern

Valide qu'une valeur correspond à un pattern regex.

```php
$rules = ['phone' => 'pattern:/^\+?[1-9]\d{1,14}$/'];
```

### Messages personnalisés

Vous pouvez personnaliser les messages d'erreur pour des champs et règles spécifiques.

```php
$validator = new Validator();
$validator->setCustomMessages([
    'email.email' => 'Veuillez fournir une adresse email valide',
    'password.min' => 'Le mot de passe doit contenir au moins :min caractères',
    'name.required' => 'Le champ nom est requis',
]);
```

### Sanitization

Par défaut, le validateur nettoie automatiquement les données d'entrée (trim des chaînes, échappement HTML).

```php
$validator = new Validator();
$validator->setSanitize(true); // Par défaut : true

$data = ['name' => '  <script>alert("xss")</script>  '];
$result = $validator->validate($data, ['name' => 'required']);

// La valeur validée sera nettoyée
$validated = $result->getValidatedValue('name');
// Résultat : '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'
```

### Règles personnalisées

Vous pouvez créer et enregistrer des règles de validation personnalisées.

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
        // Votre logique de validation
        return $value === 'expected';
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field est invalide.';
    }
}

$validator = new Validator();
$validator->registerRule(new CustomRule());

$rules = ['field' => 'custom'];
```

### Intégration avec core-php

Ce package s'intègre parfaitement avec `core-php` Forms.

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

        // Utiliser les données validées
        $validated = $result->getValidated();
        // ...
    }
}
```

## 📚 Référence API

### Validator

#### `validate(array $data, array $rules): ValidationResult`

Valide des données selon des règles.

```php
$result = $validator->validate($data, $rules);
```

#### `setCustomMessages(array $messages): self`

Définit des messages d'erreur personnalisés.

```php
$validator->setCustomMessages([
    'email.email' => 'Email invalide',
]);
```

#### `setSanitize(bool $sanitize): self`

Active ou désactive la sanitization automatique.

```php
$validator->setSanitize(false);
```

#### `registerRule(RuleInterface $rule): self`

Enregistre une règle de validation personnalisée.

```php
$validator->registerRule(new CustomRule());
```

### ValidationResult

#### `isValid(): bool`

Vérifie si la validation a réussi.

```php
if ($result->isValid()) {
    // Succès
}
```

#### `hasErrors(): bool`

Vérifie si la validation a échoué.

```php
if ($result->hasErrors()) {
    // A des erreurs
}
```

#### `getErrors(): array`

Récupère toutes les erreurs groupées par champ.

```php
$errors = $result->getErrors();
// ['email' => ['Email requis'], 'password' => ['Mot de passe trop court']]
```

#### `getFieldErrors(string $field): array`

Récupère les erreurs pour un champ spécifique.

```php
$emailErrors = $result->getFieldErrors('email');
```

#### `getFirstError(string $field): ?string`

Récupère la première erreur d'un champ.

```php
$firstError = $result->getFirstError('email');
```

#### `getValidated(): array`

Récupère toutes les données validées et nettoyées.

```php
$validated = $result->getValidated();
```

#### `getValidatedValue(string $field, mixed $default = null): mixed`

Récupère une valeur validée pour un champ spécifique.

```php
$email = $result->getValidatedValue('email');
```

## 📝 License

MIT License - Voir le fichier LICENSE pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 💝 Support

Si ce package vous est utile, envisagez de [devenir un sponsor](https://github.com/sponsors/julien-lin) pour soutenir le développement et la maintenance de ce projet open source.

---

**Développé avec ❤️ par Julien Linard**
