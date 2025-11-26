<?php

declare(strict_types=1);

namespace JulienLinard\Validator;

use JulienLinard\Validator\Exceptions\InvalidRuleException;
use JulienLinard\Validator\Rules\RuleInterface;
use JulienLinard\Validator\Rules\AbstractRule;
use JulienLinard\Validator\Translations\Translator;
use JulienLinard\Validator\Rules\RequiredRule;
use JulienLinard\Validator\Rules\EmailRule;
use JulienLinard\Validator\Rules\MinRule;
use JulienLinard\Validator\Rules\MaxRule;
use JulienLinard\Validator\Rules\NumericRule;
use JulienLinard\Validator\Rules\UrlRule;
use JulienLinard\Validator\Rules\InRule;
use JulienLinard\Validator\Rules\PatternRule;
use JulienLinard\Validator\Rules\DateRule;
use JulienLinard\Validator\Rules\BooleanRule;
use JulienLinard\Validator\Rules\BetweenRule;
use JulienLinard\Validator\Rules\FileRule;
use JulienLinard\Validator\Rules\ImageRule;
use JulienLinard\Validator\Rules\SizeRule;
use JulienLinard\Validator\Rules\AlphaRule;
use JulienLinard\Validator\Rules\AlphaNumRule;
use JulienLinard\Validator\Rules\AlphaDashRule;
use JulienLinard\Validator\Rules\ConfirmedRule;
use JulienLinard\Validator\Rules\IpRule;
use JulienLinard\Validator\Rules\Ipv4Rule;
use JulienLinard\Validator\Rules\Ipv6Rule;
use JulienLinard\Validator\Rules\JsonRule;
use JulienLinard\Validator\Rules\UuidRule;
use JulienLinard\Validator\Rules\AcceptedRule;
use JulienLinard\Validator\Rules\FilledRule;
use JulienLinard\Validator\Rules\BeforeRule;
use JulienLinard\Validator\Rules\AfterRule;
use JulienLinard\Validator\Rules\DifferentRule;
use JulienLinard\Validator\Rules\SameRule;

/**
 * Validateur principal
 */
class Validator
{
    /**
     * @var array<string, RuleInterface> Règles enregistrées
     */
    private array $rules = [];

    /**
     * @var array<string, string> Messages personnalisés par champ et règle
     */
    private array $customMessages = [];

    /**
     * @var string Langue par défaut
     */
    private string $locale = 'fr';

    /**
     * @var Translator Traducteur pour les messages d'erreur
     */
    private Translator $translator;

    /**
     * @var bool Activer la sanitization automatique
     */
    private bool $sanitize = true;

    public function __construct(string $locale = 'fr')
    {
        $this->translator = new Translator($locale);
        $this->locale = $locale;
        $this->registerDefaultRules();
    }

    /**
     * Enregistre les règles par défaut
     */
    private function registerDefaultRules(): void
    {
        $rules = [
            new RequiredRule(),
            new EmailRule(),
            new MinRule(),
            new MaxRule(),
            new NumericRule(),
            new UrlRule(),
            new InRule(),
            new PatternRule(),
            new DateRule(),
            new BooleanRule(),
            new BetweenRule(),
            new FileRule(),
            new ImageRule(),
            new SizeRule(),
            new AlphaRule(),
            new AlphaNumRule(),
            new AlphaDashRule(),
            new ConfirmedRule(),
            new IpRule(),
            new Ipv4Rule(),
            new Ipv6Rule(),
            new JsonRule(),
            new UuidRule(),
            new AcceptedRule(),
            new FilledRule(),
            new BeforeRule(),
            new AfterRule(),
            new DifferentRule(),
            new SameRule(),
        ];
        
        foreach ($rules as $rule) {
            if ($rule instanceof AbstractRule) {
                $rule->setTranslator($this->translator);
            }
            $this->registerRule($rule);
        }
    }

    /**
     * Enregistre une nouvelle règle
     */
    public function registerRule(RuleInterface $rule): self
    {
        if ($rule instanceof AbstractRule) {
            $rule->setTranslator($this->translator);
        }
        $this->rules[$rule->getName()] = $rule;
        return $this;
    }

    /**
     * Définit les messages personnalisés
     *
     * @param array<string, string|array<string>> $messages Messages personnalisés
     */
    public function setCustomMessages(array $messages): self
    {
        $this->customMessages = $messages;
        return $this;
    }

    /**
     * Définit la locale
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        $this->translator->setLocale($locale);
        
        // Mettre à jour le traducteur de toutes les règles
        foreach ($this->rules as $rule) {
            if ($rule instanceof AbstractRule) {
                $rule->setTranslator($this->translator);
            }
        }
        
        return $this;
    }
    
    /**
     * Retourne la locale actuelle
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Active ou désactive la sanitization
     */
    public function setSanitize(bool $sanitize): self
    {
        $this->sanitize = $sanitize;
        return $this;
    }

    /**
     * Valide des données avec des règles
     *
     * @param array<string, mixed> $data Données à valider
     * @param array<string, string|array> $rules Règles de validation
     * @return ValidationResult Résultat de la validation
     */
    public function validate(array $data, array $rules): ValidationResult
    {
        $result = new ValidationResult();
        $validated = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            // Convertir les règles en tableau si c'est une string
            if (is_string($fieldRules)) {
                $fieldRules = $this->parseRuleString($fieldRules);
            }

            // Sanitization si activée
            if ($this->sanitize && $value !== null) {
                $value = $this->sanitizeValue($value);
            }

            // Valider chaque règle
            foreach ($fieldRules as $ruleName => $ruleParams) {
                if (!$this->hasRule($ruleName)) {
                    throw new InvalidRuleException("Règle inconnue: {$ruleName}");
                }

                $rule = $this->rules[$ruleName];
                
                // Gestion spéciale pour les règles qui nécessitent l'accès aux autres champs
                if ($ruleName === 'confirmed') {
                    $confirmationField = $field . '_confirmation';
                    if (!isset($data[$confirmationField]) || $data[$confirmationField] !== $value) {
                        $message = $this->getErrorMessage($field, $ruleName, $ruleParams, $rule);
                        $result->addError($field, $message);
                    }
                    continue;
                }
                
                if ($ruleName === 'different' && !empty($ruleParams) && isset($ruleParams[0])) {
                    $otherField = $ruleParams[0];
                    if (isset($data[$otherField]) && $data[$otherField] === $value) {
                        $message = $this->getErrorMessage($field, $ruleName, ['other' => $otherField], $rule);
                        $result->addError($field, $message);
                    }
                    continue;
                }
                
                if ($ruleName === 'same' && !empty($ruleParams) && isset($ruleParams[0])) {
                    $otherField = $ruleParams[0];
                    if (!isset($data[$otherField]) || $data[$otherField] !== $value) {
                        $message = $this->getErrorMessage($field, $ruleName, ['other' => $otherField], $rule);
                        $result->addError($field, $message);
                    }
                    continue;
                }
                
                // Gestion spéciale pour "filled" : si le champ est présent (pas null), il ne doit pas être vide
                if ($ruleName === 'filled') {
                    // Si le champ n'est pas présent (null), c'est OK (différent de required)
                    if ($value === null) {
                        continue;
                    }
                    // Si le champ est présent, vérifier qu'il n'est pas vide
                    if (!$rule->validate($value, $ruleParams)) {
                        $message = $this->getErrorMessage($field, $ruleName, $ruleParams, $rule);
                        $result->addError($field, $message);
                    }
                    continue;
                }
                
                // Si la règle est "required", on valide même si la valeur est null
                // Sinon, on skip si la valeur est null (sauf si required)
                if ($ruleName !== 'required' && ($value === null || $value === '')) {
                    continue;
                }

                if (!$rule->validate($value, $ruleParams)) {
                    $message = $this->getErrorMessage($field, $ruleName, $ruleParams, $rule);
                    $result->addError($field, $message);
                }
            }

            // Ajouter la valeur validée si pas d'erreur
            if (!$result->hasFieldErrors($field)) {
                $validated[$field] = $value;
            }
        }

        $result->setValidated($validated);
        return $result;
    }

    /**
     * Parse une chaîne de règles (ex: "required|email|min:5")
     *
     * @return array<string, array>
     */
    private function parseRuleString(string $ruleString): array
    {
        $rules = [];
        $parts = explode('|', $ruleString);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                continue;
            }

            $ruleParts = explode(':', $part, 2);
            $ruleName = trim($ruleParts[0]);
            $ruleParams = [];

            if (isset($ruleParts[1])) {
                $paramString = trim($ruleParts[1]);
                
                // Pour les règles spéciales qui acceptent des tableaux (ex: in:value1,value2 ou between:min,max)
                if (in_array($ruleName, ['in', 'between'], true)) {
                    $params = explode(',', $paramString);
                    $ruleParams = array_map('trim', $params);
                } else {
                    // Pour les autres règles, prendre le paramètre tel quel
                    $ruleParams[] = $paramString;
                }
            }

            // Pour les règles simples, on utilise un tableau vide
            if (empty($ruleParams)) {
                $rules[$ruleName] = [];
            } else {
                // Si un seul paramètre, on le met directement
                if (count($ruleParams) === 1) {
                    $rules[$ruleName] = [0 => $ruleParams[0]];
                } else {
                    $rules[$ruleName] = $ruleParams;
                }
            }
        }

        return $rules;
    }

    /**
     * Récupère le message d'erreur pour une règle
     */
    private function getErrorMessage(
        string $field,
        string $ruleName,
        array $ruleParams,
        RuleInterface $rule
    ): string {
        // Vérifier si un message personnalisé existe
        $customKey = "{$field}.{$ruleName}";
        if (isset($this->customMessages[$customKey])) {
            $message = $this->customMessages[$customKey];
        } elseif (isset($this->customMessages[$field][$ruleName])) {
            $message = $this->customMessages[$field][$ruleName];
        } elseif (isset($this->customMessages[$ruleName])) {
            $message = $this->customMessages[$ruleName];
        } else {
            // Utiliser le message par défaut de la règle
            $message = $rule->getMessage($field, $ruleParams);
        }

        // Les placeholders sont déjà remplacés par AbstractRule via le Translator
        // On garde cette partie pour la compatibilité avec les messages personnalisés
        if ($rule instanceof AbstractRule) {
            // Extraire les paramètres pour le remplacement (si message personnalisé)
            $params = [];
            if (isset($ruleParams[0])) {
                $params['min'] = $ruleParams[0];
                $params['max'] = $ruleParams[0];
            }
            if (isset($ruleParams[1])) {
                $params['max'] = $ruleParams[1];
            }
            if (isset($ruleParams['allowed'])) {
                $params['allowed'] = implode(', ', $ruleParams['allowed']);
            } elseif ($rule->getName() === 'in' && !empty($ruleParams)) {
                // Pour la règle "in", formater la liste
                $allowed = is_array($ruleParams[0]) ? $ruleParams[0] : $ruleParams;
                $params['allowed'] = implode(', ', $allowed);
            }

            $message = str_replace(':field', $field, $message);
            foreach ($params as $key => $value) {
                $message = str_replace(":{$key}", (string)$value, $message);
            }
        }

        return $message;
    }

    /**
     * Sanitize une valeur
     */
    private function sanitizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            // Nettoyer les espaces
            $value = trim($value);
            // Échapper les caractères HTML
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        } elseif (is_array($value)) {
            $value = array_map([$this, 'sanitizeValue'], $value);
        }

        return $value;
    }

    /**
     * Vérifie si une règle existe
     */
    public function hasRule(string $ruleName): bool
    {
        return isset($this->rules[$ruleName]);
    }

    /**
     * Récupère une règle
     */
    public function getRule(string $ruleName): ?RuleInterface
    {
        return $this->rules[$ruleName] ?? null;
    }
}

