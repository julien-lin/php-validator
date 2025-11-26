<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Translations;

/**
 * Gestionnaire de traductions pour les messages d'erreur
 */
class Translator
{
    /**
     * @var array<string, array<string, string>> Traductions chargées
     */
    private array $translations = [];

    /**
     * @var string Langue actuelle
     */
    private string $locale = 'fr';

    /**
     * Langues supportées
     */
    private const SUPPORTED_LOCALES = ['fr', 'en', 'es'];

    /**
     * Constructeur
     */
    public function __construct(string $locale = 'fr')
    {
        $this->setLocale($locale);
    }

    /**
     * Définit la langue
     */
    public function setLocale(string $locale): self
    {
        if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'fr'; // Fallback vers français
        }
        
        $this->locale = $locale;
        $this->loadTranslations($locale);
        
        return $this;
    }

    /**
     * Retourne la langue actuelle
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Charge les traductions pour une langue
     */
    private function loadTranslations(string $locale): void
    {
        if (isset($this->translations[$locale])) {
            return; // Déjà chargé
        }

        $translationFile = __DIR__ . "/{$locale}.php";
        
        if (file_exists($translationFile)) {
            $this->translations[$locale] = require $translationFile;
        } else {
            // Fallback vers français si la langue n'existe pas
            $this->translations[$locale] = require __DIR__ . "/fr.php";
        }
    }

    /**
     * Traduit un message
     *
     * @param string $key Clé de traduction (ex: "required", "email.invalid")
     * @param array<string, mixed> $replacements Remplacements pour les placeholders
     * @return string Message traduit
     */
    public function translate(string $key, array $replacements = []): string
    {
        $message = $this->translations[$this->locale][$key] ?? $key;
        
        // Remplacer les placeholders
        foreach ($replacements as $placeholder => $value) {
            $message = str_replace(":{$placeholder}", (string)$value, $message);
        }
        
        return $message;
    }

    /**
     * Vérifie si une traduction existe
     */
    public function hasTranslation(string $key): bool
    {
        return isset($this->translations[$this->locale][$key]);
    }
}

