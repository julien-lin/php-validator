<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Rules;

/**
 * Règle : validation d'image uploadée
 */
class ImageRule extends AbstractRule
{
    public function getName(): string
    {
        return 'image';
    }

    public function validate(mixed $value, array $params = []): bool
    {
        if ($value === null || $value === '') {
            return true; // Les images optionnelles sont gérées par la règle "required"
        }

        // Vérifier que c'est un tableau de fichier uploadé
        if (!is_array($value)) {
            return false;
        }

        // Vérifier la structure d'un fichier uploadé PHP
        if (!isset($value['error']) || !isset($value['tmp_name'])) {
            return false;
        }

        // Vérifier qu'il n'y a pas d'erreur d'upload
        if ($value['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Vérifier que le fichier existe
        if (!file_exists($value['tmp_name'])) {
            return false;
        }

        // Vérifier que c'est une vraie image avec getimagesize()
        $imageInfo = @getimagesize($value['tmp_name']);
        if ($imageInfo === false) {
            return false;
        }

        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $value['tmp_name']);
        finfo_close($finfo);

        // Vérifier la taille si spécifiée (en bytes) - paramètre 0
        if (isset($params[0]) && is_numeric($params[0])) {
            $maxSize = (int)$params[0];
            if (isset($value['size']) && $value['size'] > $maxSize) {
                return false;
            }
        }

        // Vérifier les types MIME si spécifiés - paramètre 1
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/svg+xml'];
        
        if (isset($params[1]) && is_array($params[1])) {
            $allowedTypes = $params[1];
        }

        if (!in_array($mimeType, $allowedTypes, true)) {
            return false;
        }

        return true;
    }

    protected function getDefaultMessage(): string
    {
        return 'Le champ :field doit être une image valide.';
    }
}

