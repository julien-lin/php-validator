<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation UUID (v1, v2, v3, v4, v5)
 */
class UuidRule extends AbstractRule
{
    public function getName(): string
    {
        return 'uuid';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (!is_string($value)) {
            return false;
        }

        // Pattern UUID standard (RFC 4122)
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        return (bool)preg_match($pattern, $value);
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être un UUID valide.';
    }
}

