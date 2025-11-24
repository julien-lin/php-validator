<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : champ requis
 */
class RequiredRule extends AbstractRule
{
    public function getName(): string
    {
        return 'required';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        
        return $value !== null && $value !== '';
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field est requis.';
    }
}

