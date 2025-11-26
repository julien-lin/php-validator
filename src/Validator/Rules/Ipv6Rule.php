<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation d'adresse IPv6
 */
class Ipv6Rule extends AbstractRule
{
    public function getName(): string
    {
        return 'ipv6';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (!is_string($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une adresse IPv6 valide.';
    }
}

