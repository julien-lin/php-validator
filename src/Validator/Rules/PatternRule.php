<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : pattern regex
 */
class PatternRule extends AbstractRule
{
    public function getName(): string
    {
        return 'pattern';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if (!is_string($value)) {
            return false;
        }
        
        $pattern = $params['pattern'] ?? $params[0] ?? null;
        if ($pattern === null) {
            return false;
        }
        
        return preg_match($pattern, $value) === 1;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field ne correspond pas au format requis.';
    }
}

