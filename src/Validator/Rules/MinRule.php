<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : longueur minimale
 */
class MinRule extends AbstractRule
{
    public function getName(): string
    {
        return 'min';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }
        
        $min = $params['min'] ?? $params[0] ?? null;
        if ($min === null) {
            return false;
        }
        
        if (is_string($value)) {
            return mb_strlen($value) >= (int)$min;
        }
        
        return (float)$value >= (float)$min;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit contenir au moins :min caractères.';
    }
}

