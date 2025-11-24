<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : valeur numérique
 */
class NumericRule extends AbstractRule
{
    public function getName(): string
    {
        return 'numeric';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        return is_numeric($value);
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être un nombre.';
    }
}

