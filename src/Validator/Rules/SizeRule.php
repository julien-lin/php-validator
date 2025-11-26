<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation de taille (pour fichiers ou strings)
 */
class SizeRule extends AbstractRule
{
    public function getName(): string
    {
        return 'size';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les valeurs optionnelles sont gérées par la règle "required"
        }

        if (empty($params) || !isset($params[0])) {
            return false; // Besoin d'une taille
        }

        $expectedSize = (int)$params[0];

        // Pour les fichiers uploadés
        if (is_array($value) && isset($value['size'])) {
            return (int)$value['size'] === $expectedSize;
        }

        // Pour les strings, utiliser la longueur
        if (is_string($value)) {
            return mb_strlen($value) === $expectedSize;
        }

        // Pour les nombres
        if (is_numeric($value)) {
            return (int)$value === $expectedSize;
        }

        return false;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit avoir une taille de :size.';
    }
}

