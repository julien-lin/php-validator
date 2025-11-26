<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation JSON
 */
class JsonRule extends AbstractRule
{
    public function getName(): string
    {
        return 'json';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (!is_string($value)) {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une chaîne JSON valide.';
    }
}

