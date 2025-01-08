<?php
// dar_like.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexion.php';

function crearNotificacionLike($enlace, $id_publicacion, $usuario_id)
{
    $stmt = $enlace->prepare("INSERT INTO notificaciones (usuario_id, publicacion_id, tipo) VALUES (?, ?, 'like')");
    $stmt->bind_param("ii", $usuario_id, $id_publicacion);
    $stmt->execute();
}

// Verificar conexión a la base de datos
if ($enlace->connect_error) {
    die("Error de conexión: " . $enlace->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar input
    $id_publicacion = filter_input(INPUT_POST, 'id_publicacion', FILTER_VALIDATE_INT);

    if (!$id_publicacion) {
        echo "error|ID de publicación inválido";
        exit();
    }

    // Verificar si existe la publicación primero
    $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "error|Publicación no encontrada";
        exit();
    }

    // Si no hay sesión iniciada, devolver el conteo actual de likes
    if (!isset($_SESSION['usuario_id'])) {
        $row = $result->fetch_assoc();
        echo "info|" . $row['cantidad_megusta'];
        exit();
    }

    try {
        $enlace->begin_transaction();

        // Verificar si ya existe el like
        $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insertar like
            $stmt = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
            $stmt->execute();

            // Actualizar contador
            $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();

            // Crear notificación
            crearNotificacionLike($enlace, $id_publicacion, $_SESSION['usuario_id']);
        } else {
            // Eliminar like
            $stmt = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
            $stmt->execute();

            // Actualizar contador
            $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta - 1 WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();
        }

        // Obtener nuevo conteo
        $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $enlace->commit();

        echo ($result->num_rows == 0 ? "liked" : "unliked") . "|" . $row['cantidad_megusta'];
    } catch (Exception $e) {
        $enlace->rollback();
        echo "error|" . $e->getMessage();
    }
}
