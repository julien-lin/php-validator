<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation "before" (date avant une autre date)
 */
class BeforeRule extends AbstractRule
{
    public function getName(): string
    {
        return 'before';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (empty($params) || !isset($params[0])) {
            return false; // Besoin d'une date de référence
        }

        try {
            $valueDate = new \DateTime($value);
            $referenceDate = new \DateTime($params[0]);
            
            return $valueDate < $referenceDate;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une date antérieure à :value.';
    }
}

