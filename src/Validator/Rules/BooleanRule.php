<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation de booléen
 */
class BooleanRule extends AbstractRule
{
    public function getName(): string
    {
        return 'boolean';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les booléens optionnels sont gérés par la règle "required"
        }

        // Accepter : true, false, 1, 0, "1", "0", "true", "false", "yes", "no", "on", "off"
        if (is_bool($value)) {
            return true;
        }

        if (is_numeric($value)) {
            return in_array((int)$value, [0, 1], true);
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));
            return in_array($lower, ['1', '0', 'true', 'false', 'yes', 'no', 'on', 'off'], true);
        }

        return false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être un booléen (true/false, 1/0, yes/no).';
    }
}

