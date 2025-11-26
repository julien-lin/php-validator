<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation "filled" (champ rempli, non vide)
 * Différent de "required" : "filled" vérifie que si le champ est présent, il n'est pas vide
 */
class FilledRule extends AbstractRule
{
    public function getName(): string
    {
        return 'filled';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        // Si null, c'est OK (le champ n'est pas présent)
        if ($value === null) {
            return true;
        }

        // Si c'est une chaîne, vérifier qu'elle n'est pas vide après trim
        if (is_string($value)) {
            return trim($value) !== '';
        }

        // Si c'est un tableau, vérifier qu'il n'est pas vide
        if (is_array($value)) {
            return !empty($value);
        }

        // Pour les autres types, vérifier qu'ils ne sont pas vides
        return $value !== '';
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être rempli.';
    }
}

