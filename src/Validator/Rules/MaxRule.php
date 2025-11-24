<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : longueur maximale
 */
class MaxRule extends AbstractRule
{
    public function getName(): string
    {
        return 'max';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }
        
        $max = $params['max'] ?? $params[0] ?? null;
        if ($max === null) {
            return false;
        }
        
        if (is_string($value)) {
            return mb_strlen($value) <= (int)$max;
        }
        
        return (float)$value <= (float)$max;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field ne doit pas dépasser :max caractères.';
    }
}

