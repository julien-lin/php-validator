# Architecture du Validateur

## Vue d'ensemble

Le validateur `php-validator` est un système de validation avancé pour PHP 8+ qui permet de valider des données avec des règles personnalisables, des messages multilingues, une validation conditionnelle et une sanitization automatique.

## Composants principaux

### 1. Validator (Classe principale)

La classe `Validator` est le composant central qui gère :
- L'enregistrement des règles
- La validation des données
- La gestion des messages d'erreur
- La sanitization des données
- La traduction des messages

**Structure interne** :
```php
class Validator
{
    // Règles enregistrées
    private array $rules = [];
    
    // Messages personnalisés
    private array $customMessages = [];
    
    // Langue par défaut
    private string $locale = 'fr';
    
    // Traducteur
    private Translator $translator;
    
    // Sanitization activée
    private bool $sanitize = true;
}
```

### 2. RuleInterface et AbstractRule

**RuleInterface** : Interface que toutes les règles doivent implémenter
```php
interface RuleInterface
{
    public function validate(mixed $value, array $params = []): bool;
    public function getMessage(string $field, array $params = []): string;
}
```

**AbstractRule** : Classe abstraite de base qui facilite la création de règles
- Gère automatiquement la traduction des messages
- Fournit des méthodes utilitaires pour le remplacement des placeholders
- Simplifie l'implémentation des règles personnalisées

### 3. ValidationResult

La classe `ValidationResult` encapsule le résultat d'une validation :
- Erreurs par champ
- Données validées et sanitizées
- Méthodes pour vérifier la validité et récupérer les erreurs

**Structure** :
```php
class ValidationResult
{
    private array $errors = [];      // Erreurs par champ
    private array $validated = [];   // Données validées
}
```

### 4. Translator

Le `Translator` gère les traductions des messages d'erreur :
- Support de plusieurs langues (fr, en, es)
- Remplacement des placeholders (`:field`, `:min`, `:max`, etc.)
- Fallback vers le français si la langue n'est pas disponible

## Flux d'exécution

### 1. Initialisation

```php
$validator = new Validator('fr'); // Locale par défaut
```

**Processus** :
1. Création du `Translator` avec la locale
2. Enregistrement automatique des règles par défaut (30 règles)
3. Configuration de la sanitization (activée par défaut)

### 2. Validation

```php
$result = $validator->validate($data, $rules);
```

**Processus détaillé** :

1. **Parsing des règles** :
   - Conversion des règles string (`"required|email|min:5"`) en tableau
   - Extraction des paramètres pour chaque règle

2. **Pour chaque champ** :
   - Récupération de la valeur depuis `$data`
   - **Sanitization** (si activée) :
     - Trim des espaces
     - Échappement HTML (`htmlspecialchars`)
   - **Validation de chaque règle** :
     - Vérification de l'existence de la règle
     - Appel de `$rule->validate($value, $params)`
     - Gestion spéciale pour certaines règles :
       - `confirmed` : Vérifie `{field}_confirmation`
       - `different` : Compare avec un autre champ
       - `same` : Compare avec un autre champ
       - `filled` : Vérifie si présent et non vide

3. **Gestion des erreurs** :
   - Si une règle échoue, récupération du message d'erreur
   - Utilisation des messages personnalisés si définis
   - Sinon, utilisation du traducteur
   - Ajout de l'erreur au `ValidationResult`

4. **Données validées** :
   - Si le champ n'a pas d'erreurs, ajout à `$validated`
   - Retour du `ValidationResult`

### 3. Récupération des résultats

```php
if ($result->isValid()) {
    $validated = $result->getValidated();
} else {
    $errors = $result->getErrors();
}
```

## Règles spéciales

### Règles nécessitant l'accès aux autres champs

Certaines règles nécessitent l'accès aux autres champs de données :
- `confirmed` : Compare avec `{field}_confirmation`
- `different` : Compare avec un autre champ spécifié
- `same` : Compare avec un autre champ spécifié

Ces règles sont gérées directement dans `Validator::validate()` et non dans la méthode `validate()` de la règle elle-même.

### Règles optionnelles

Par défaut, toutes les règles (sauf `required`) sont optionnelles :
- Si la valeur est `null` ou `''`, la validation est ignorée
- La règle `required` valide même les valeurs vides
- La règle `filled` vérifie que si le champ est présent, il n'est pas vide

## Sanitization

La sanitization est activée par défaut et effectue :
- **Trim** : Suppression des espaces en début et fin
- **Échappement HTML** : `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`
- **Récursif** : Pour les tableaux, sanitization de chaque élément

**Désactivation** :
```php
$validator->setSanitize(false);
```

**Note** : La sanitization peut affecter certaines validations (ex: JSON avec guillemets échappés). Dans ce cas, désactiver la sanitization pour ces champs spécifiques.

## Messages personnalisés

Les messages personnalisés peuvent être définis au niveau global ou par champ/règle :

```php
$validator->setCustomMessages([
    'email.email' => 'Email invalide personnalisé',
    'password.min' => 'Le mot de passe doit contenir au moins :min caractères',
]);
```

**Format** : `{field}.{rule}` ou `{rule}` pour tous les champs

## Traduction

Le système de traduction supporte 3 langues :
- `fr` (français) - par défaut
- `en` (anglais)
- `es` (espagnol)

**Changement de locale** :
```php
$validator->setLocale('en');
```

**Placeholders supportés** :
- `:field` : Nom du champ
- `:min` : Valeur minimale
- `:max` : Valeur maximale
- `:allowed` : Valeurs autorisées (pour `in`)
- `:value` : Valeur de référence (pour `before`, `after`)
- `:other` : Autre champ (pour `different`, `same`)
- `:size` : Taille requise

## Enregistrement de règles personnalisées

```php
$validator->registerRule(new CustomRule());
```

Les règles personnalisées doivent :
- Implémenter `RuleInterface` ou étendre `AbstractRule`
- Définir `getName()` pour retourner le nom de la règle
- Implémenter `validate($value, $params)` pour la logique de validation
- Définir `getDefaultMessage()` pour le message par défaut

## Performance

### Optimisations

1. **Cache des règles** : Les règles sont enregistrées une seule fois et réutilisées
2. **Parsing optimisé** : Les règles string sont parsées une seule fois par validation
3. **Traduction mise en cache** : Les fichiers de traduction sont chargés une seule fois

### Recommandations

- Utiliser des règles simples quand possible
- Éviter les regex complexes dans `PatternRule`
- Désactiver la sanitization si non nécessaire pour améliorer les performances

## Sécurité

### Protection XSS

La sanitization par défaut échappe les caractères HTML pour prévenir les attaques XSS.

### Validation des entrées

Toutes les entrées sont validées avant d'être utilisées, réduisant les risques d'injection.

### Messages d'erreur

Les messages d'erreur ne doivent pas exposer d'informations sensibles sur la structure de l'application.

