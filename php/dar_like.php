<?php
// dar_like.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

function crearNotificacionLike($enlace, $id_publicacion, $usuario_id) {
    // Add your notification creation logic here
    $stmt = $enlace->prepare("INSERT INTO notificaciones (usuario_id, publicacion_id, tipo) VALUES (?, ?, 'like')");
    $stmt->bind_param("ii", $usuario_id, $id_publicacion);
    $stmt->execute();
}

// Verify database connection
if ($enlace->connect_error) {
    die("Error de conexión: " . $enlace->connect_error);
}

// Verify user is logged in
if (!isset($_SESSION['usuario_id'])) {
    echo "error|Debes iniciar sesión para dar like";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $id_publicacion = filter_input(INPUT_POST, 'id_publicacion', FILTER_VALIDATE_INT);

    if (!$id_publicacion) {
        echo "error|ID de publicación inválido";
        exit();
    }

    try {
        // Start transaction
        $enlace->begin_transaction();

        // Check if like exists
        $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert like
            $stmt = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
            $stmt->execute();

            // Update like count
            $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();

            // Create like notification
            crearNotificacionLike($enlace, $id_publicacion, $_SESSION['usuario_id']);

            // Get new like count
            $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Commit transaction
            $enlace->commit();

            echo "liked|" . $row['cantidad_megusta'];
        } else {
            // Remove like
            $stmt = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
            $stmt->execute();

            // Update like count
            $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta - 1 WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();

            // Get new like count
            $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Commit transaction
            $enlace->commit();

            echo "unliked|" . $row['cantidad_megusta'];
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $enlace->rollback();
        echo "error|" . $e->getMessage();
    }
}
