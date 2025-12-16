# Changelog

Tous les changements notables de ce projet seront documentés dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

## [1.4.1] - 2025-01-15

### 📚 Documentation Technique (Phase 1.2)

- **Documentation complète** : Création de 4 fichiers de documentation technique
  - `DOCUMENTATION/ARCHITECTURE.md` : Architecture détaillée du validateur
    - Composants principaux (Validator, RuleInterface, AbstractRule, ValidationResult, Translator)
    - Flux d'exécution complet
    - Règles spéciales et optionnelles
    - Sanitization et sécurité
    - Performance et optimisations
  - `DOCUMENTATION/RULES.md` : Liste complète des 30 règles de validation
    - Règles de base (required, email, min, max, numeric, url, in, pattern)
    - Règles de date (date, before, after)
    - Règles de type (boolean, json, uuid)
    - Règles de comparaison (between, size, different, same, confirmed)
    - Règles de format (alpha, alpha_num, alpha_dash)
    - Règles réseau (ip, ipv4, ipv6)
    - Règles de fichiers (file, image)
    - Règles conditionnelles (filled, accepted)
    - Tableau récapitulatif avec paramètres
  - `DOCUMENTATION/CUSTOM_RULES.md` : Guide complet pour créer des règles personnalisées
    - Approche recommandée avec AbstractRule
    - Exemples pratiques (phone, min_age, unique, exists)
    - Règles avec paramètres
    - Règles nécessitant l'accès aux autres champs
    - Bonnes pratiques et tests
  - `DOCUMENTATION/TRANSLATIONS.md` : Système de traduction
    - Langues supportées (fr, en, es)
    - Structure des fichiers de traduction
    - Placeholders disponibles
    - Ajout d'une nouvelle langue
    - Messages personnalisés
    - Exemples et bonnes pratiques

## [1.4.0] - 2025-01-15

### 🧪 Tests Supplémentaires (Phase 1.1)

- **Augmentation drastique de la couverture de tests** : Passage de 13 à 121 tests (165 assertions)
  - Nouveau fichier `RulesTest.php` : Tests complets pour toutes les 30 règles de validation
    - Tests pour chaque règle (required, email, min, max, numeric, url, in, pattern, date, boolean, between, alpha, alpha_num, alpha_dash, confirmed, ip, ipv4, ipv6, json, uuid, accepted, filled, before, after, different, same, size)
    - Tests pour les cas de succès et d'échec
    - Tests pour les cas limites et les valeurs optionnelles
    - Total : 108 tests pour les règles
  - Nouveau fichier `MultilingualTest.php` : Tests pour les messages multilingues
    - Tests pour les locales français, anglais et espagnol
    - Tests pour le changement de locale
    - Tests pour les messages personnalisés qui remplacent la locale
    - Tests pour le remplacement des paramètres dans les messages
    - Total : 9 tests
  - Nouveau fichier `SanitizationTest.php` : Tests pour la sanitization
    - Tests pour la suppression des balises HTML
    - Tests pour le trim des espaces
    - Tests pour l'échappement des entités HTML
    - Tests pour la désactivation de la sanitization
    - Tests pour la sanitization des tableaux
    - Total : 7 tests
  - Nouveau fichier `ConditionalValidationTest.php` : Tests pour la validation conditionnelle
    - Tests pour les règles conditionnelles (required, filled, different, same, before, after)
    - Tests pour les validations complexes avec plusieurs règles
    - Tests pour les scénarios réels (formulaire d'inscription)
    - Total : 12 tests

### 📊 Statistiques

- **Avant** : 13 tests, 20 assertions (~20% de couverture)
- **Après** : 121 tests, 165 assertions (objectif 80%+ de couverture)
- **Augmentation** : +108 tests, +145 assertions
- **Lignes de code de tests** : 1206 lignes

