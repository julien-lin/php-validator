# Système de Traduction

## Vue d'ensemble

Le validateur `php-validator` supporte nativement 3 langues : français (par défaut), anglais et espagnol. Le système de traduction est extensible et permet d'ajouter de nouvelles langues facilement.

## Langues supportées

- **`fr`** (français) - Langue par défaut
- **`en`** (anglais)
- **`es`** (espagnol)

## Utilisation

### Définir la locale

```php
// À la création
$validator = new Validator('en');

// Ou après création
$validator->setLocale('en');
```

### Récupérer la locale actuelle

```php
$locale = $validator->getLocale(); // 'en'
```

## Structure des fichiers de traduction

Les fichiers de traduction sont situés dans `src/Validator/Translations/` :
- `fr.php` - Traductions françaises
- `en.php` - Traductions anglaises
- `es.php` - Traductions espagnoles

### Format d'un fichier de traduction

```php
<?php

/**
 * Traductions françaises pour les messages d'erreur de validation
 */
return [
    'required' => 'Le champ :field est requis.',
    'email' => 'Le champ :field doit être un email valide.',
    'min' => 'Le champ :field doit contenir au moins :min caractères.',
    'max' => 'Le champ :field ne peut pas dépasser :max caractères.',
    // ...
];
```

## Placeholders

Les messages de traduction supportent des placeholders qui sont automatiquement remplacés :

### Placeholders disponibles

| Placeholder | Description | Exemple |
|------------|-------------|---------|
| `:field` | Nom du champ | `email`, `password` |
| `:min` | Valeur minimale | `8` (pour `min:8`) |
| `:max` | Valeur maximale | `100` (pour `max:100`) |
| `:allowed` | Valeurs autorisées | `active, inactive` (pour `in:active,inactive`) |
| `:value` | Valeur de référence | `2023-01-01` (pour `before:2023-01-01`) |
| `:other` | Autre champ | `old_password` (pour `different:old_password`) |
| `:size` | Taille requise | `5` (pour `size:5`) |

### Exemples de remplacement

```php
// Règle : min:8
// Message : 'Le champ :field doit contenir au moins :min caractères.'
// Résultat : 'Le champ password doit contenir au moins 8 caractères.'

// Règle : between:10,20
// Message : 'Le champ :field doit être entre :min et :max.'
// Résultat : 'Le champ age doit être entre 10 et 20.'

// Règle : in:active,inactive
// Message : 'Le champ :field doit être l'une des valeurs suivantes : :allowed.'
// Résultat : 'Le champ status doit être l'une des valeurs suivantes : active, inactive.'
```

## Ajouter une nouvelle langue

### 1. Créer le fichier de traduction

Créez un nouveau fichier dans `src/Validator/Translations/` :

```php
<?php

/**
 * Traductions allemandes pour les messages d'erreur de validation
 */
return [
    'required' => 'Das Feld :field ist erforderlich.',
    'email' => 'Das Feld :field muss eine gültige E-Mail-Adresse sein.',
    'min' => 'Das Feld :field muss mindestens :min Zeichen enthalten.',
    'max' => 'Das Feld :field darf nicht mehr als :max Zeichen enthalten.',
    'numeric' => 'Das Feld :field muss eine Zahl sein.',
    'url' => 'Das Feld :field muss eine gültige URL sein.',
    'in' => 'Das Feld :field muss einer der folgenden Werte sein: :allowed.',
    'pattern' => 'Das Feld :field entspricht nicht dem erforderlichen Format.',
    'date' => 'Das Feld :field muss ein gültiges Datum sein.',
    'boolean' => 'Das Feld :field muss ein boolescher Wert sein (true/false, 1/0, yes/no).',
    'between' => 'Das Feld :field muss zwischen :min und :max liegen.',
    'file' => 'Das Feld :field muss eine gültige Datei sein.',
    'image' => 'Das Feld :field muss ein gültiges Bild sein.',
    'size' => 'Das Feld :field muss eine Größe von :size haben.',
    'alpha' => 'Das Feld :field darf nur Buchstaben enthalten.',
    'alpha_num' => 'Das Feld :field darf nur Buchstaben und Zahlen enthalten.',
    'alpha_dash' => 'Das Feld :field darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'confirmed' => 'Die Bestätigung des Feldes :field stimmt nicht überein.',
    'ip' => 'Das Feld :field muss eine gültige IP-Adresse sein.',
    'ipv4' => 'Das Feld :field muss eine gültige IPv4-Adresse sein.',
    'ipv6' => 'Das Feld :field muss eine gültige IPv6-Adresse sein.',
    'json' => 'Das Feld :field muss eine gültige JSON-Zeichenkette sein.',
    'uuid' => 'Das Feld :field muss eine gültige UUID sein.',
    'accepted' => 'Das Feld :field muss akzeptiert werden.',
    'filled' => 'Das Feld :field muss einen Wert haben.',
    'before' => 'Das Feld :field muss ein Datum vor :value sein.',
    'after' => 'Das Feld :field muss ein Datum nach :value sein.',
    'different' => 'Das Feld :field muss sich von :other unterscheiden.',
    'same' => 'Das Feld :field muss mit :other übereinstimmen.',
];
```

### 2. Mettre à jour Translator

Modifiez `src/Validator/Translations/Translator.php` :

```php
private const SUPPORTED_LOCALES = ['fr', 'en', 'es', 'de']; // Ajouter 'de'
```

### 3. Utiliser la nouvelle langue

```php
$validator = new Validator('de');
$result = $validator->validate($data, $rules);
```

## Messages personnalisés

Les messages personnalisés remplacent les traductions par défaut :

### Au niveau global

```php
$validator->setCustomMessages([
    'email' => 'Email invalide personnalisé',
    'min' => 'Minimum :min caractères requis',
]);
```

### Par champ et règle

```php
$validator->setCustomMessages([
    'email.email' => 'L\'adresse email n\'est pas valide',
    'password.min' => 'Le mot de passe doit contenir au moins :min caractères',
]);
```

**Format** : `{field}.{rule}` pour un champ spécifique, ou `{rule}` pour tous les champs.

### Priorité

1. Messages personnalisés (priorité la plus haute)
2. Traductions de la locale
3. Message par défaut de la règle (fallback)

## Exemples

### Exemple 1 : Validation avec locale anglaise

```php
$validator = new Validator('en');
$result = $validator->validate(
    ['email' => 'invalid'],
    ['email' => 'email']
);

$error = $result->getFirstError('email');
// "The email field must be a valid email address."
```

### Exemple 2 : Validation avec locale espagnole

```php
$validator = new Validator('es');
$result = $validator->validate(
    ['age' => '15'],
    ['age' => 'between:18,100']
);

$error = $result->getFirstError('age');
// "El campo age debe estar entre 18 y 100."
```

### Exemple 3 : Messages personnalisés avec placeholders

```php
$validator = new Validator('fr');
$validator->setCustomMessages([
    'password.min' => 'Le mot de passe doit contenir au moins :min caractères pour être sécurisé',
]);

$result = $validator->validate(
    ['password' => '123'],
    ['password' => 'min:8']
);

$error = $result->getFirstError('password');
// "Le mot de passe doit contenir au moins 8 caractères pour être sécurisé"
```

### Exemple 4 : Changement de locale dynamique

```php
$validator = new Validator('fr');

// Validation en français
$result1 = $validator->validate($data, $rules);

// Changer la locale
$validator->setLocale('en');

// Validation en anglais
$result2 = $validator->validate($data, $rules);
```

## Fallback

Si une traduction n'existe pas pour une règle dans la locale sélectionnée, le système utilise :
1. Le message par défaut de la règle (défini dans `getDefaultMessage()`)
2. Si la locale n'est pas supportée, fallback vers le français

## Bonnes pratiques

### 1. Utiliser des placeholders

Toujours utiliser des placeholders dans les messages pour les rendre dynamiques :

```php
// ✅ Bon
'min' => 'Le champ :field doit contenir au moins :min caractères.'

// ❌ Mauvais
'min' => 'Le champ doit contenir au moins 8 caractères.'
```

### 2. Messages cohérents

Maintenir une cohérence dans le style des messages :

```php
// Style cohérent
'required' => 'Le champ :field est requis.',
'email' => 'Le champ :field doit être un email valide.',
'min' => 'Le champ :field doit contenir au moins :min caractères.',
```

### 3. Messages informatifs

Les messages doivent être clairs et informatifs :

```php
// ✅ Bon : Message clair
'email' => 'Le champ :field doit être une adresse email valide (ex: user@example.com).'

// ❌ Mauvais : Message trop vague
'email' => 'Email invalide.'
```

### 4. Traductions complètes

Assurez-vous que toutes les règles ont des traductions dans toutes les langues supportées.

## Extension du système

Pour étendre le système de traduction (ex: charger depuis une base de données), vous pouvez créer un `Translator` personnalisé :

```php
class DatabaseTranslator extends Translator
{
    private $database;

    public function __construct(string $locale, $database)
    {
        parent::__construct($locale);
        $this->database = $database;
    }

    protected function loadTranslations(string $locale): void
    {
        // Charger depuis la base de données
        $translations = $this->database->query(
            "SELECT rule_key, message FROM translations WHERE locale = ?",
            [$locale]
        );
        
        $this->translations[$locale] = $translations;
    }
}
```

Puis l'utiliser dans un `Validator` personnalisé.

