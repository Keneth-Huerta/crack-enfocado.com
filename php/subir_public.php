<?php
require_once 'ImageHandler.php';
require_once 'conexion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../secion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['contenido']);
    $usuario_id = $_SESSION['usuario_id'];
    $imagen_path = null;

    // Validar contenido
    if (empty($content) && empty($_FILES['image']['name'])) {
        $_SESSION['mensaje'] = "No puedes publicar un mensaje vacío.";
        $_SESSION['mensaje_tipo'] = "danger";
        header("Location: publicaciones.php");
        exit();
    }

    try {
        // Procesar la imagen si existe
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $imageHandler = new ImageHandler();
            $imagen_path = $imageHandler->uploadImage($_FILES['image']);

            if (!$imagen_path) {
                throw new Exception("Error al subir la imagen. Verifica el formato y tamaño.");
            }
        }

        // Guardar la publicación en la base de datos
        $query = "INSERT INTO publicaciones (usuario_id, contenido, imagen) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $content, $imagen_path);

        if (!mysqli_stmt_execute($stmt)) {
            // Si falla la inserción, eliminar la imagen si se subió
            if ($imagen_path) {
                $imageHandler->deleteImage($imagen_path);
            }
            throw new Exception("Error al guardar la publicación: " . mysqli_error($enlace));
        }

        $_SESSION['mensaje'] = "Publicación creada exitosamente";
        $_SESSION['mensaje_tipo'] = "success";
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error: " . $e->getMessage();
        $_SESSION['mensaje_tipo'] = "danger";
    }

    header("Location: publicaciones.php");
    exit();
}
