<?php
require_once 'conexion.php';
require_once 'ImageHandler.php';

class ImageMigration
{
    private $db;
    private $imageHandler;
    private $logFile;

    public function __construct($db)
    {
        $this->db = $db;
        $this->imageHandler = new ImageHandler();
        $this->logFile = dirname(__FILE__) . '/migration_log.txt';

        // Crear directorio de uploads si no existe
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/media/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    }

    private function logMessage($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message\n", FILE_APPEND);
        echo "$message\n";
    }

    public function migrateProductImages()
    {
        $this->logMessage("Iniciando migración de imágenes de productos...");

        $query = "SELECT idProducto, imagen FROM productos WHERE imagen IS NOT NULL";
        $result = mysqli_query($this->db, $query);

        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            try {
                if (empty($row['imagen'])) continue;

                // Generar nombre único para la imagen
                $fileName = 'product_' . $row['idProducto'] . '_' . uniqid() . '.jpg';
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/media/uploads/' . $fileName;

                // Guardar imagen en el sistema de archivos
                file_put_contents($filePath, $row['imagen']);

                // Actualizar registro en la base de datos
                $relativePath = '/media/uploads/' . $fileName;
                $updateQuery = "UPDATE productos SET imagen = ? WHERE idProducto = ?";
                $stmt = mysqli_prepare($this->db, $updateQuery);
                mysqli_stmt_bind_param($stmt, "si", $relativePath, $row['idProducto']);

                if (mysqli_stmt_execute($stmt)) {
                    $count++;
                    $this->logMessage("Migrada imagen del producto {$row['idProducto']}");
                } else {
                    $this->logMessage("Error al actualizar producto {$row['idProducto']}: " . mysqli_error($this->db));
                }

                mysqli_stmt_close($stmt);
            } catch (Exception $e) {
                $this->logMessage("Error en producto {$row['idProducto']}: " . $e->getMessage());
            }
        }

        $this->logMessage("Migración de productos completada. Total migrado: $count");
    }

    public function migrateProfileImages()
    {
        $this->logMessage("Iniciando migración de imágenes de perfiles...");

        // Migrar fotos de perfil
        $query = "SELECT id, foto_perfil, foto_portada FROM perfiles WHERE foto_perfil IS NOT NULL OR foto_portada IS NOT NULL";
        $result = mysqli_query($this->db, $query);

        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            try {
                $updates = [];
                $params = [];
                $types = "";

                // Procesar foto de perfil
                if (!empty($row['foto_perfil']) && strpos($row['foto_perfil'], '/media/uploads/') === false) {
                    $fileName = 'profile_' . $row['id'] . '_' . uniqid() . '.jpg';
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/media/uploads/' . $fileName;
                    file_put_contents($filePath, $row['foto_perfil']);

                    $updates[] = "foto_perfil = ?";
                    $params[] = '/media/uploads/' . $fileName;
                    $types .= "s";
                }

                // Procesar foto de portada
                if (!empty($row['foto_portada']) && strpos($row['foto_portada'], '/media/uploads/') === false) {
                    $fileName = 'cover_' . $row['id'] . '_' . uniqid() . '.jpg';
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/media/uploads/' . $fileName;
                    file_put_contents($filePath, $row['foto_portada']);

                    $updates[] = "foto_portada = ?";
                    $params[] = '/media/uploads/' . $fileName;
                    $types .= "s";
                }

                if (!empty($updates)) {
                    $params[] = $row['id'];
                    $types .= "i";

                    $updateQuery = "UPDATE perfiles SET " . implode(", ", $updates) . " WHERE id = ?";
                    $stmt = mysqli_prepare($this->db, $updateQuery);
                    mysqli_stmt_bind_param($stmt, $types, ...$params);

                    if (mysqli_stmt_execute($stmt)) {
                        $count++;
                        $this->logMessage("Migradas imágenes del perfil {$row['id']}");
                    } else {
                        $this->logMessage("Error al actualizar perfil {$row['id']}: " . mysqli_error($this->db));
                    }

                    mysqli_stmt_close($stmt);
                }
            } catch (Exception $e) {
                $this->logMessage("Error en perfil {$row['id']}: " . $e->getMessage());
            }
        }

        $this->logMessage("Migración de perfiles completada. Total migrado: $count");
    }

    public function migratePublicationImages()
    {
        $this->logMessage("Iniciando migración de imágenes de publicaciones...");

        $query = "SELECT id_publicacion, imagen FROM publicaciones WHERE imagen IS NOT NULL";
        $result = mysqli_query($this->db, $query);

        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            try {
                if (empty($row['imagen']) || strpos($row['imagen'], '/media/uploads/') !== false) continue;

                $fileName = 'post_' . $row['id_publicacion'] . '_' . uniqid() . '.jpg';
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/media/uploads/' . $fileName;

                // Guardar imagen en el sistema de archivos
                file_put_contents($filePath, $row['imagen']);

                // Actualizar registro en la base de datos
                $relativePath = '/media/uploads/' . $fileName;
                $updateQuery = "UPDATE publicaciones SET imagen = ? WHERE id_publicacion = ?";
                $stmt = mysqli_prepare($this->db, $updateQuery);
                mysqli_stmt_bind_param($stmt, "si", $relativePath, $row['id_publicacion']);

                if (mysqli_stmt_execute($stmt)) {
                    $count++;
                    $this->logMessage("Migrada imagen de la publicación {$row['id_publicacion']}");
                } else {
                    $this->logMessage("Error al actualizar publicación {$row['id_publicacion']}: " . mysqli_error($this->db));
                }

                mysqli_stmt_close($stmt);
            } catch (Exception $e) {
                $this->logMessage("Error en publicación {$row['id_publicacion']}: " . $e->getMessage());
            }
        }

        $this->logMessage("Migración de publicaciones completada. Total migrado: $count");
    }

    public function migrate()
    {
        $this->logMessage("=== Iniciando proceso de migración ===");

        $this->migrateProductImages();
        $this->migrateProfileImages();
        $this->migratePublicationImages();

        $this->logMessage("=== Migración completada ===");
    }
}

// Ejecutar la migración
$migration = new ImageMigration($enlace);
$migration->migrate();
