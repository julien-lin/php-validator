<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : email valide
 */
class EmailRule extends AbstractRule
{
    public function getName(): string
    {
        return 'email';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if (!is_string($value) || empty($value)) {
            return false;
        }
        
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être un email valide.';
    }
}

