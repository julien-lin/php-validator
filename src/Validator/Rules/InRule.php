<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : valeur dans une liste
 */
class InRule extends AbstractRule
{
    public function getName(): string
    {
        return 'in';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        // Les paramètres peuvent être dans params[0] (tableau) ou params (tableau direct)
        $allowed = $params[0] ?? $params;
        
        // Si c'est une string, la convertir en tableau
        if (is_string($allowed)) {
            $allowed = explode(',', $allowed);
            $allowed = array_map('trim', $allowed);
        }
        
        if (!is_array($allowed)) {
            return false;
        }
        
        return in_array($value, $allowed, true);
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une des valeurs autorisées.';
    }
}

