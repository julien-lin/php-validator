<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Interface pour les règles de validation
 */
interface RuleInterface
{
    /**
     * Valide une valeur
     *
     * @param mixed $value Valeur à valider
     * @param array $params Paramètres de la règle
     * @return bool True si la validation réussit
     */
    public function validate(mixed $value, array $params = []): bool;

    /**
     * Retourne le message d'erreur par défaut
     */
    public function getMessage(string $field, array $params = []): string;
}

