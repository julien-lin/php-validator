<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation "accepted" (yes, on, 1, true, "yes", "on")
 * Utilisé pour les cases à cocher, conditions d'utilisation, etc.
 */
class AcceptedRule extends AbstractRule
{
    public function getName(): string
    {
        return 'accepted';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return false; // "accepted" nécessite une valeur positive
        }

        // Accepter : true, 1, "1", "true", "yes", "on"
        if (is_bool($value)) {
            return $value === true;
        }

        if (is_numeric($value)) {
            return (int)$value === 1;
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));
            return in_array($lower, ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être accepté.';
    }
}

