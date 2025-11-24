<?php

declare(strict_types=1);

namespace JulienLinard\Validator;

use JulienLinard\Validator\Exceptions\InvalidRuleException;
use JulienLinard\Validator\Rules\RuleInterface;
use JulienLinard\Validator\Rules\AbstractRule;
use JulienLinard\Validator\Rules\RequiredRule;
use JulienLinard\Validator\Rules\EmailRule;
use JulienLinard\Validator\Rules\MinRule;
use JulienLinard\Validator\Rules\MaxRule;
use JulienLinard\Validator\Rules\NumericRule;
use JulienLinard\Validator\Rules\UrlRule;
use JulienLinard\Validator\Rules\InRule;
use JulienLinard\Validator\Rules\PatternRule;

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
     * @var bool Activer la sanitization automatique
     */
    private bool $sanitize = true;

    public function __construct()
    {
        $this->registerDefaultRules();
    }

    /**
     * Enregistre les règles par défaut
     */
    private function registerDefaultRules(): void
    {
        $this->registerRule(new RequiredRule());
        $this->registerRule(new EmailRule());
        $this->registerRule(new MinRule());
        $this->registerRule(new MaxRule());
        $this->registerRule(new NumericRule());
        $this->registerRule(new UrlRule());
        $this->registerRule(new InRule());
        $this->registerRule(new PatternRule());
    }

    /**
     * Enregistre une nouvelle règle
     */
    public function registerRule(RuleInterface $rule): self
    {
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
        return $this;
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
                $params = explode(',', $ruleParts[1]);
                foreach ($params as $param) {
                    $param = trim($param);
                    // Si c'est un tableau (ex: in:value1,value2)
                    if (strpos($param, ',') !== false) {
                        $ruleParams = array_map('trim', explode(',', $param));
                    } else {
                        $ruleParams[] = $param;
                    }
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

        // Remplacer les placeholders dans les paramètres
        if ($rule instanceof AbstractRule) {
            // Extraire les paramètres pour le remplacement
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

