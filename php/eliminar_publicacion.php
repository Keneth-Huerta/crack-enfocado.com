<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el ID de la publicación fue pasado por GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $publicacion_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verificar si la publicación existe y pertenece al usuario
    $query = "SELECT * FROM publicaciones WHERE id_publicacion = ? AND usuario_id = ?";
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "ii", $publicacion_id, $usuario_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($resultado) > 0) {
                // La publicación existe, proceder a eliminarla
                $delete_query = "DELETE FROM publicaciones WHERE id_publicacion = ?";
                if ($delete_stmt = mysqli_prepare($enlace, $delete_query)) {
                    mysqli_stmt_bind_param($delete_stmt, "i", $publicacion_id);
                    if (mysqli_stmt_execute($delete_stmt)) {
                        // Redirigir al perfil después de eliminar la publicación
                        header("Location: perfil.php?mensaje=Publicación eliminada con éxito");
                        exit();
                    } else {
                        echo "Error al eliminar la publicación.";
                    }
                }
            } else {
                echo "No tienes permiso para eliminar esta publicación.";
            }
        }
    }
} else {
    echo "ID de publicación no válido.";
}
?>
