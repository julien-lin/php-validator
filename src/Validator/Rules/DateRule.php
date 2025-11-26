<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation de date
 */
class DateRule extends AbstractRule
{
    public function getName(): string
    {
        return 'date';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les dates optionnelles sont gérées par la règle "required"
        }

        if (!is_string($value)) {
            return false;
        }

        // Format de date optionnel (par défaut: Y-m-d H:i:s ou Y-m-d)
        $format = $params[0] ?? null;

        if ($format) {
            $date = \DateTime::createFromFormat($format, $value);
            return $date !== false && $date->format($format) === $value;
        }

        // Essayer les formats courants
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une date valide.';
    }
}

