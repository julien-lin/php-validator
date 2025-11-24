<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Classe abstraite de base pour les règles de validation
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * Retourne le nom de la règle
     */
    abstract public function getName(): string;

    /**
     * Retourne le message d'erreur par défaut
     */
    public function getMessage(string $field, array $params = []): string
    {
        $message = $this->getDefaultMessage();
        
        // Remplace les placeholders
        $message = str_replace(':field', $field, $message);
        
        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", (string)$value, $message);
        }
        
        return $message;
    }

    /**
     * Retourne le message d'erreur par défaut
     */
    abstract protected function getDefaultMessage(): string;
}

