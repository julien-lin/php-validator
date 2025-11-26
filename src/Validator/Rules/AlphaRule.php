<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation alpha (lettres uniquement)
 */
class AlphaRule extends AbstractRule
{
    public function getName(): string
    {
        return 'alpha';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (!is_string($value)) {
            return false;
        }

        // Vérifier que la chaîne contient uniquement des lettres (y compris les caractères accentués)
        return (bool)preg_match('/^[\p{L}\s]+$/u', $value);
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field ne doit contenir que des lettres.';
    }
}

