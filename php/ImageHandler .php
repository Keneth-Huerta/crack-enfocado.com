<?php
class ImageHandler
{
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;

    public function __construct()
    {
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/media/uploads/';
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $this->maxSize = 5 * 1024 * 1024; // 5MB

        // Crear directorio si no existe
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function uploadImage($file)
    {
        try {
            // Validaciones
            if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Error en la subida del archivo');
            }

            if (!in_array($file['type'], $this->allowedTypes)) {
                throw new Exception('Tipo de archivo no permitido');
            }

            if ($file['size'] > $this->maxSize) {
                throw new Exception('El archivo excede el tamaño máximo permitido');
            }

            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $filePath = $this->uploadDir . $fileName;

            // Mover archivo
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception('Error al mover el archivo');
            }

            // Retornar ruta relativa para almacenar en BD
            return '/media/uploads/' . $fileName;
        } catch (Exception $e) {
            error_log('Error al subir imagen: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteImage($filePath)
    {
        try {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filePath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log('Error al eliminar imagen: ' . $e->getMessage());
            return false;
        }
    }

    public function optimizeImage($filePath)
    {
        // Aquí puedes agregar lógica para optimizar imágenes
        // Por ejemplo, comprimir o redimensionar
    }
}
