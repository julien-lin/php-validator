<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation de confirmation (ex: password_confirmation)
 * 
 * Cette règle nécessite que le champ ait un champ de confirmation correspondant.
 * Exemple: 'password' => 'confirmed' vérifie que 'password_confirmation' existe et correspond.
 */
class ConfirmedRule extends AbstractRule
{
    public function getName(): string
    {
        return 'confirmed';
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
        return 'Le champ :field doit être confirmé.';
    }
}

