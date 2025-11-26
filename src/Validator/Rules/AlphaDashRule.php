<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation alpha-dash (lettres, chiffres, tirets et underscores)
 */
class AlphaDashRule extends AbstractRule
{
    public function getName(): string
    {
        return 'alpha_dash';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (!is_string($value)) {
            return false;
        }

        // Vérifier que la chaîne contient uniquement des lettres, chiffres, tirets et underscores
        return (bool)preg_match('/^[\p{L}\p{N}\-_]+$/u', $value);
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field ne doit contenir que des lettres, chiffres, tirets et underscores.';
    }
}

