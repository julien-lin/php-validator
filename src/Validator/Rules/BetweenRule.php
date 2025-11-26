<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation entre deux valeurs (min et max)
 */
class BetweenRule extends AbstractRule
{
    public function getName(): string
    {
        return 'between';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (empty($params) || count($params) < 2) {
            return false; // Besoin de min et max
        }

        $min = $params[0] ?? null;
        $max = $params[1] ?? null;

        if ($min === null || $max === null) {
            return false;
        }

        // Convertir en numérique si possible
        if (is_numeric($value)) {
            $numValue = (float)$value;
            $minNum = (float)$min;
            $maxNum = (float)$max;
            return $numValue >= $minNum && $numValue <= $maxNum;
        }

        // Pour les strings, utiliser la longueur
        if (is_string($value)) {
            $length = mb_strlen($value);
            $minNum = (int)$min;
            $maxNum = (int)$max;
            return $length >= $minNum && $length <= $maxNum;
        }

        return false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être entre :min et :max.';
    }
}

