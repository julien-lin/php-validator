<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

use JulienLinard\Validator\Translations\Translator;

/**
 * Classe abstraite de base pour les règles de validation
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * @var Translator|null Traducteur pour les messages
     */
    protected ?Translator $translator = null;

    /**
     * Retourne le nom de la règle
     */
    abstract public function getName(): string;

    /**
     * Définit le traducteur
     */
    public function setTranslator(?Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Retourne le message d'erreur
     */
    public function getMessage(string $field, array $params = []): string
    {
        $ruleName = $this->getName();
        $replacements = ['field' => $field];
        
        // Extraire les paramètres selon le type de règle
        if ($ruleName === 'between' && count($params) >= 2) {
            $replacements['min'] = $params[0];
            $replacements['max'] = $params[1];
        } elseif ($ruleName === 'in' && !empty($params)) {
            // Pour "in", les paramètres peuvent être dans params[0] ou params directement
            $allowed = $params[0] ?? $params;
            if (is_string($allowed)) {
                $allowed = explode(',', $allowed);
                $allowed = array_map('trim', $allowed);
            }
            $replacements['allowed'] = is_array($allowed) ? implode(', ', $allowed) : '';
        } elseif (in_array($ruleName, ['before', 'after'], true) && isset($params[0])) {
            $replacements['value'] = $params[0];
        } elseif (in_array($ruleName, ['min', 'max', 'size'], true) && isset($params[0])) {
            $replacements[$ruleName] = $params[0];
        } elseif (isset($params[0])) {
            // Pour les autres règles, utiliser le premier paramètre comme valeur générique
            $replacements['value'] = $params[0];
        }
        
        // Utiliser le traducteur si disponible
        if ($this->translator !== null) {
            return $this->translator->translate($ruleName, $replacements);
        }
        
        // Fallback vers le message par défaut si pas de traducteur
        $message = $this->getDefaultMessage();
        
        // Remplace les placeholders
        foreach ($replacements as $key => $value) {
            $message = str_replace(":{$key}", (string)$value, $message);
        }
        
        return $message;
    }

    /**
     * Retourne le message d'erreur par défaut (fallback)
     */
    abstract protected function getDefaultMessage(): string;
}

