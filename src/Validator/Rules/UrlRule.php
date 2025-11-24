<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : URL valide
 */
class UrlRule extends AbstractRule
{
    public function getName(): string
    {
        return 'url';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if (!is_string($value) || empty($value)) {
            return false;
        }
        
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une URL valide.';
    }
}

