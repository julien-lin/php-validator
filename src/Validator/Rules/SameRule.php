<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation "same" (même valeur qu'un autre champ)
 * 
 * Cette règle nécessite l'accès aux autres champs de données.
 * La validation complète se fait dans Validator.
 */
class SameRule extends AbstractRule
{
    public function getName(): string
    {
        return 'same';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        // Cette règle doit être vérifiée au niveau du Validator car elle nécessite
        // l'accès aux autres champs de données
        // On retourne true ici, la vraie validation se fait dans Validator
        return true;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit correspondre à :other.';
    }
}

