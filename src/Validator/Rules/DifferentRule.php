<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation "different" (valeur différente d'un autre champ)
 * 
 * Cette règle nécessite l'accès aux autres champs de données.
 * La validation complète se fait dans Validator.
 */
class DifferentRule extends AbstractRule
{
    public function getName(): string
    {
        return 'different';
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
        return 'Le champ :field doit être différent de :other.';
    }
}

