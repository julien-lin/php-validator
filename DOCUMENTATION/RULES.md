# Liste Complète des Règles

## Vue d'ensemble

Le validateur `php-validator` fournit 30 règles de validation prêtes à l'emploi, couvrant la plupart des cas d'usage courants.

## Règles de base

### required

Le champ est requis (ne peut pas être vide).

```php
$rules = ['name' => 'required'];
```

**Comportement** :
- Échoue si la valeur est `null`, `''` ou une chaîne vide après trim
- Passe si la valeur est présente et non vide

**Exemple** :
```php
$data = ['name' => ''];
$result = $validator->validate($data, ['name' => 'required']);
// Échoue : Le champ name est requis.
```

### email

Le champ doit être une adresse email valide.

```php
$rules = ['email' => 'email'];
```

**Exemple** :
```php
$data = ['email' => 'user@example.com'];
$result = $validator->validate($data, ['email' => 'email']);
// Passe
```

### min

Le champ doit contenir au moins N caractères.

```php
$rules = ['password' => 'min:8'];
```

**Exemple** :
```php
$data = ['password' => '12345'];
$result = $validator->validate($data, ['password' => 'min:8']);
// Échoue : Le champ password doit contenir au moins 8 caractères.
```

### max

Le champ ne peut pas dépasser N caractères.

```php
$rules = ['title' => 'max:100'];
```

**Exemple** :
```php
$data = ['title' => str_repeat('a', 101)];
$result = $validator->validate($data, ['title' => 'max:100']);
// Échoue : Le champ title ne peut pas dépasser 100 caractères.
```

### numeric

Le champ doit être un nombre (entier ou décimal).

```php
$rules = ['age' => 'numeric'];
```

**Exemple** :
```php
$data = ['age' => '25'];
$result = $validator->validate($data, ['age' => 'numeric']);
// Passe

$data = ['age' => 'not-a-number'];
$result = $validator->validate($data, ['age' => 'numeric']);
// Échoue : Le champ age doit être un nombre.
```

### url

Le champ doit être une URL valide.

```php
$rules = ['website' => 'url'];
```

**Exemple** :
```php
$data = ['website' => 'https://example.com'];
$result = $validator->validate($data, ['website' => 'url']);
// Passe
```

### in

Le champ doit être l'une des valeurs spécifiées.

```php
$rules = ['status' => 'in:active,inactive,pending'];
```

**Exemple** :
```php
$data = ['status' => 'active'];
$result = $validator->validate($data, ['status' => 'in:active,inactive']);
// Passe

$data = ['status' => 'invalid'];
$result = $validator->validate($data, ['status' => 'in:active,inactive']);
// Échoue : Le champ status doit être l'une des valeurs suivantes : active, inactive.
```

### pattern

Le champ doit correspondre à une expression régulière.

```php
$rules = ['code' => 'pattern:/^\d+$/'];
```

**Exemple** :
```php
$data = ['code' => '12345'];
$result = $validator->validate($data, ['code' => 'pattern:/^\d+$/']);
// Passe

$data = ['code' => 'ABC'];
$result = $validator->validate($data, ['code' => 'pattern:/^\d+$/']);
// Échoue : Le champ code ne correspond pas au format requis.
```

## Règles de date

### date

Le champ doit être une date valide.

```php
$rules = ['birthday' => 'date'];
```

**Formats supportés** :
- `Y-m-d` (ex: 2023-01-15)
- `Y-m-d H:i:s` (ex: 2023-01-15 14:30:00)
- Formats standards PHP (`DateTime::createFromFormat`)

**Avec format personnalisé** :
```php
$rules = ['date' => 'date:d/m/Y'];
$data = ['date' => '15/01/2023'];
// Passe
```

### before

Le champ doit être une date avant la date spécifiée.

```php
$rules = ['end_date' => 'before:2023-12-31'];
```

**Exemple** :
```php
$data = ['end_date' => '2022-12-31'];
$result = $validator->validate($data, ['end_date' => 'before:2023-12-31']);
// Passe
```

### after

Le champ doit être une date après la date spécifiée.

```php
$rules = ['start_date' => 'after:2023-01-01'];
```

**Exemple** :
```php
$data = ['start_date' => '2024-01-01'];
$result = $validator->validate($data, ['start_date' => 'after:2023-01-01']);
// Passe
```

## Règles de type

### boolean

Le champ doit être un booléen.

**Valeurs acceptées** :
- `true`, `false`
- `'1'`, `'0'`
- `'yes'`, `'no'`
- `'on'`, `'off'`

```php
$rules = ['active' => 'boolean'];
```

### json

Le champ doit être une chaîne JSON valide.

```php
$rules = ['data' => 'json'];
```

**Exemple** :
```php
$data = ['data' => '{"key":"value"}'];
$result = $validator->validate($data, ['data' => 'json']);
// Passe (note: désactiver la sanitization pour les JSON)
```

**Important** : Désactiver la sanitization pour les champs JSON car elle échappe les guillemets.

### uuid

Le champ doit être un UUID valide (version 4).

```php
$rules = ['id' => 'uuid'];
```

**Exemple** :
```php
$data = ['id' => '550e8400-e29b-41d4-a716-446655440000'];
$result = $validator->validate($data, ['id' => 'uuid']);
// Passe
```

## Règles de comparaison

### between

Le champ doit être entre deux valeurs (incluses).

```php
$rules = ['age' => 'between:18,100'];
```

**Exemple** :
```php
$data = ['age' => '25'];
$result = $validator->validate($data, ['age' => 'between:18,100']);
// Passe

$data = ['age' => '15'];
$result = $validator->validate($data, ['age' => 'between:18,100']);
// Échoue : Le champ age doit être entre 18 et 100.
```

### size

Le champ doit avoir exactement N caractères.

```php
$rules = ['code' => 'size:5'];
```

**Exemple** :
```php
$data = ['code' => '12345'];
$result = $validator->validate($data, ['code' => 'size:5']);
// Passe
```

### different

Le champ doit être différent d'un autre champ.

```php
$rules = ['new_password' => 'different:old_password'];
```

**Exemple** :
```php
$data = [
    'old_password' => 'old',
    'new_password' => 'new'
];
$result = $validator->validate($data, ['new_password' => 'different:old_password']);
// Passe

$data = [
    'old_password' => 'same',
    'new_password' => 'same'
];
$result = $validator->validate($data, ['new_password' => 'different:old_password']);
// Échoue : Le champ new_password doit être différent de old_password.
```

### same

Le champ doit correspondre à un autre champ.

```php
$rules = ['password_confirm' => 'same:password'];
```

**Exemple** :
```php
$data = [
    'password' => 'secret',
    'password_confirm' => 'secret'
];
$result = $validator->validate($data, ['password_confirm' => 'same:password']);
// Passe
```

### confirmed

Le champ doit être confirmé par un champ `{field}_confirmation`.

```php
$rules = ['password' => 'confirmed'];
```

**Exemple** :
```php
$data = [
    'password' => 'secret',
    'password_confirmation' => 'secret'
];
$result = $validator->validate($data, ['password' => 'confirmed']);
// Passe

$data = [
    'password' => 'secret',
    'password_confirmation' => 'different'
];
$result = $validator->validate($data, ['password' => 'confirmed']);
// Échoue : Le champ password doit être confirmé.
```

## Règles de format

### alpha

Le champ ne doit contenir que des lettres (y compris les caractères accentués).

```php
$rules = ['name' => 'alpha'];
```

**Exemple** :
```php
$data = ['name' => 'José'];
$result = $validator->validate($data, ['name' => 'alpha']);
// Passe

$data = ['name' => 'John123'];
$result = $validator->validate($data, ['name' => 'alpha']);
// Échoue : Le champ name ne doit contenir que des lettres.
```

### alpha_num

Le champ ne doit contenir que des lettres et des chiffres.

```php
$rules = ['username' => 'alpha_num'];
```

**Exemple** :
```php
$data = ['username' => 'user123'];
$result = $validator->validate($data, ['username' => 'alpha_num']);
// Passe

$data = ['username' => 'user-name'];
$result = $validator->validate($data, ['username' => 'alpha_num']);
// Échoue : Le champ username ne doit contenir que des lettres et des chiffres.
```

### alpha_dash

Le champ ne doit contenir que des lettres, chiffres, tirets et underscores.

```php
$rules = ['slug' => 'alpha_dash'];
```

**Exemple** :
```php
$data = ['slug' => 'my-slug_123'];
$result = $validator->validate($data, ['slug' => 'alpha_dash']);
// Passe

$data = ['slug' => 'my slug'];
$result = $validator->validate($data, ['slug' => 'alpha_dash']);
// Échoue : Le champ slug ne doit contenir que des lettres, chiffres, tirets et underscores.
```

## Règles réseau

### ip

Le champ doit être une adresse IP valide (IPv4 ou IPv6).

```php
$rules = ['ip' => 'ip'];
```

**Exemple** :
```php
$data = ['ip' => '192.168.1.1'];
$result = $validator->validate($data, ['ip' => 'ip']);
// Passe

$data = ['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
$result = $validator->validate($data, ['ip' => 'ip']);
// Passe
```

### ipv4

Le champ doit être une adresse IPv4 valide.

```php
$rules = ['ip' => 'ipv4'];
```

**Exemple** :
```php
$data = ['ip' => '192.168.1.1'];
$result = $validator->validate($data, ['ip' => 'ipv4']);
// Passe

$data = ['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
$result = $validator->validate($data, ['ip' => 'ipv4']);
// Échoue : Le champ ip doit être une adresse IPv4 valide.
```

### ipv6

Le champ doit être une adresse IPv6 valide.

```php
$rules = ['ip' => 'ipv6'];
```

**Exemple** :
```php
$data = ['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
$result = $validator->validate($data, ['ip' => 'ipv6']);
// Passe
```

## Règles de fichiers

### file

Le champ doit être un fichier valide.

```php
$rules = ['document' => 'file'];
```

**Note** : Cette règle vérifie que la valeur est un tableau de fichier uploadé (`$_FILES`).

### image

Le champ doit être une image valide.

```php
$rules = ['photo' => 'image'];
```

**Note** : Cette règle vérifie que le fichier est une image (extension et type MIME).

## Règles conditionnelles

### filled

Si le champ est présent, il ne doit pas être vide.

```php
$rules = ['optional_field' => 'filled'];
```

**Comportement** :
- Si le champ n'est pas présent (`null`), la validation passe
- Si le champ est présent mais vide (`''`), la validation échoue
- Si le champ est présent et rempli, la validation passe

**Exemple** :
```php
$data = [];
$result = $validator->validate($data, ['optional_field' => 'filled']);
// Passe (champ absent)

$data = ['optional_field' => ''];
$result = $validator->validate($data, ['optional_field' => 'filled']);
// Échoue : Le champ optional_field doit avoir une valeur.

$data = ['optional_field' => 'value'];
$result = $validator->validate($data, ['optional_field' => 'filled']);
// Passe
```

### accepted

Le champ doit être accepté (pour les cases à cocher, termes, etc.).

**Valeurs acceptées** :
- `'yes'`
- `'on'`
- `'1'`
- `true`
- `1`

```php
$rules = ['terms' => 'accepted'];
```

**Exemple** :
```php
$data = ['terms' => 'yes'];
$result = $validator->validate($data, ['terms' => 'accepted']);
// Passe

$data = ['terms' => 'no'];
$result = $validator->validate($data, ['terms' => 'accepted']);
// Échoue : Le champ terms doit être accepté.
```

## Combinaison de règles

Plusieurs règles peuvent être combinées avec le séparateur `|` :

```php
$rules = [
    'email' => 'required|email|max:255',
    'password' => 'required|min:8|confirmed',
    'age' => 'required|numeric|between:18,100'
];
```

**Ordre d'exécution** :
1. Les règles sont exécutées dans l'ordre défini
2. Si une règle échoue, les erreurs sont collectées
3. Toutes les règles sont évaluées (pas d'arrêt au premier échec)

## Règles optionnelles

Par défaut, toutes les règles (sauf `required`) sont optionnelles :
- Si la valeur est `null` ou `''`, la validation est ignorée
- Pour forcer la validation même si vide, utiliser `required` en premier

**Exemple** :
```php
// Optionnel mais si présent, doit être un email valide
$rules = ['email' => 'email'];

// Requis ET doit être un email valide
$rules = ['email' => 'required|email'];
```

## Tableau récapitulatif

| Règle | Paramètres | Description |
|-------|-----------|-------------|
| `required` | - | Champ requis |
| `email` | - | Email valide |
| `min` | `:N` | Minimum N caractères |
| `max` | `:N` | Maximum N caractères |
| `numeric` | - | Nombre |
| `url` | - | URL valide |
| `in` | `:val1,val2,...` | Valeur dans la liste |
| `pattern` | `:/regex/` | Expression régulière |
| `date` | `:format` (optionnel) | Date valide |
| `boolean` | - | Booléen |
| `between` | `:min,max` | Entre deux valeurs |
| `alpha` | - | Lettres uniquement |
| `alpha_num` | - | Lettres et chiffres |
| `alpha_dash` | - | Lettres, chiffres, tirets, underscores |
| `confirmed` | - | Confirmation requise |
| `ip` | - | Adresse IP (IPv4 ou IPv6) |
| `ipv4` | - | Adresse IPv4 |
| `ipv6` | - | Adresse IPv6 |
| `json` | - | Chaîne JSON valide |
| `uuid` | - | UUID valide |
| `accepted` | - | Accepté (yes, on, 1, true) |
| `filled` | - | Si présent, non vide |
| `before` | `:date` | Date avant |
| `after` | `:date` | Date après |
| `different` | `:field` | Différent d'un autre champ |
| `same` | `:field` | Identique à un autre champ |
| `file` | - | Fichier valide |
| `image` | - | Image valide |
| `size` | `:N` | Exactement N caractères |

