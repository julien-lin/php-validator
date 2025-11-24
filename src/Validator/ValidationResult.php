<?php

declare(strict_types=1);

namespace JulienLinard\Validator;

/**
 * Résultat d'une validation
 */
class ValidationResult
{
    /**
     * @var array<string, array<string>> Erreurs par champ
     */
    private array $errors = [];

    /**
     * @var array<string, mixed> Données validées et sanitizées
     */
    private array $validated = [];

    /**
     * Ajoute une erreur pour un champ
     */
    public function addError(string $field, string $message): self
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
        return $this;
    }

    /**
     * Ajoute plusieurs erreurs pour un champ
     */
    public function addErrors(string $field, array $messages): self
    {
        foreach ($messages as $message) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /**
     * Définit les données validées
     */
    public function setValidated(array $data): self
    {
        $this->validated = $data;
        return $this;
    }

    /**
     * Vérifie si la validation a réussi
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Vérifie si la validation a échoué
     */
    public function hasErrors(): bool
    {
        return !$this->isValid();
    }

    /**
     * Récupère toutes les erreurs
     *
     * @return array<string, array<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Récupère les erreurs pour un champ spécifique
     *
     * @return array<string>
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Récupère la première erreur d'un champ
     */
    public function getFirstError(string $field): ?string
    {
        $errors = $this->getFieldErrors($field);
        return $errors[0] ?? null;
    }

    /**
     * Récupère toutes les erreurs sous forme de tableau plat
     *
     * @return array<string>
     */
    public function getAllErrors(): array
    {
        $allErrors = [];
        foreach ($this->errors as $fieldErrors) {
            $allErrors = array_merge($allErrors, $fieldErrors);
        }
        return $allErrors;
    }

    /**
     * Récupère les données validées
     *
     * @return array<string, mixed>
     */
    public function getValidated(): array
    {
        return $this->validated;
    }

    /**
     * Récupère une valeur validée
     */
    public function getValidatedValue(string $field, mixed $default = null): mixed
    {
        return $this->validated[$field] ?? $default;
    }

    /**
     * Vérifie si un champ a des erreurs
     */
    public function hasFieldErrors(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }

    /**
     * Efface toutes les erreurs
     */
    public function clearErrors(): self
    {
        $this->errors = [];
        return $this;
    }
}

