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

- ✅ **Règles intégrées** : 30+ règles incluant required, email, min, max, numeric, url, in, pattern, date, boolean, between, file, image, size, alpha, alpha_num, alpha_dash, confirmed, ip, ipv4, ipv6, json, uuid, accepted, filled, before, after, different, same
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

#### Date

Valide qu'un champ contient une date valide.

```php
$rules = [
    'birthday' => 'date',                    // N'importe quel format de date valide
    'created_at' => 'date:Y-m-d H:i:s',     // Format spécifique
];
```

#### Boolean

Valide qu'un champ est une valeur booléenne. Accepte : true, false, 1, 0, "1", "0", "true", "false", "yes", "no", "on", "off".

```php
$rules = ['is_active' => 'boolean'];
```

#### Between

Valide qu'une valeur est entre deux nombres (pour les numériques) ou a une longueur entre deux valeurs (pour les chaînes).

```php
$rules = [
    'age' => 'between:18,65',        // Numérique : entre 18 et 65
    'title' => 'between:5,100',      // Longueur de chaîne : entre 5 et 100 caractères
];
```

#### File

Valide qu'un champ est un fichier uploadé valide.

```php
$rules = [
    'document' => 'file',           // N'importe quel fichier
    'document' => 'file:10485760',  // Max 10MB (en bytes)
];

// Pour la validation du type MIME, utiliser le format array :
$rules = [
    'document' => [
        'file' => [10485760, ['application/pdf', 'application/msword']]
    ]
];
```

#### Image

Valide qu'un champ est un fichier image valide. Vérifie automatiquement le type MIME et utilise `getimagesize()` pour s'assurer que c'est une vraie image.

```php
$rules = [
    'avatar' => 'image',           // N'importe quelle image
    'avatar' => 'image:10485760',  // Max 10MB (en bytes)
];

// Pour des types d'image spécifiques, utiliser le format array :
$rules = [
    'avatar' => [
        'image' => [10485760, ['image/jpeg', 'image/png']]  // Taille max d'abord, puis types autorisés
    ]
];
```

#### Size

Valide qu'un champ a une taille exacte (pour les fichiers : bytes, pour les chaînes : caractères, pour les nombres : valeur exacte).

```php
$rules = [
    'code' => 'size:6',          // Chaîne : exactement 6 caractères
    'file' => 'size:1024',        // Fichier : exactement 1024 bytes
    'count' => 'size:10',        // Nombre : exactement 10
];
```

#### Alpha

Valide qu'un champ contient uniquement des lettres (y compris les caractères accentués).

```php
$rules = ['name' => 'alpha'];
```

#### Alpha Num

Valide qu'un champ contient uniquement des lettres et des chiffres.

```php
$rules = ['username' => 'alpha_num'];
```

#### Alpha Dash

Valide qu'un champ contient uniquement des lettres, chiffres, tirets et underscores.

```php
$rules = ['slug' => 'alpha_dash'];
```

#### Confirmed

Valide qu'un champ a un champ de confirmation correspondant (ex: `password_confirmation`).

```php
$rules = ['password' => 'required|confirmed'];
// Nécessite que le champ 'password_confirmation' corresponde à 'password'
```

#### Adresse IP

Valide qu'un champ est une adresse IP valide.

```php
$rules = [
    'ip' => 'ip',        // IPv4 ou IPv6
    'ip' => 'ipv4',      // IPv4 uniquement
    'ip' => 'ipv6',      // IPv6 uniquement
];
```

#### JSON

Valide qu'un champ contient une chaîne JSON valide.

```php
$rules = ['config' => 'json'];
```

#### UUID

Valide qu'un champ est un UUID valide (v1-v5).

```php
$rules = ['id' => 'uuid'];
```

#### Accepted

Valide qu'un champ est accepté (yes, on, 1, true). Utile pour les cases à cocher et l'acceptation de conditions.

```php
$rules = ['terms' => 'accepted'];
```

#### Filled

Valide qu'un champ a une valeur lorsqu'il est présent (différent de required - autorise null si le champ n'est pas présent).

```php
$rules = ['optional_field' => 'filled'];
```

#### Before / After

Valide qu'un champ date est avant ou après une autre date.

```php
$rules = [
    'start_date' => 'date|before:end_date',
    'end_date' => 'date|after:start_date',
    'birthday' => 'date|before:today',  // ou 'before:2024-01-01'
];
```

#### Different / Same

Valide qu'un champ est différent de ou identique à un autre champ.

```php
$rules = [
    'new_password' => 'different:old_password',
    'password_confirmation' => 'same:password',
];
```

### Support multilingue

Le validateur supporte plusieurs langues (Français, Anglais, Espagnol) nativement.

```php
// Créer un validateur avec une langue spécifique
$validator = new Validator('en'); // Anglais
$validator = new Validator('fr'); // Français (par défaut)
$validator = new Validator('es'); // Espagnol

// Ou changer la langue après création
$validator = new Validator();
$validator->setLocale('en');

// Obtenir la langue actuelle
$locale = $validator->getLocale(); // Retourne 'en', 'fr', ou 'es'
```

**Langues supportées :**
- `fr` - Français (par défaut)
- `en` - Anglais
- `es` - Espagnol

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
