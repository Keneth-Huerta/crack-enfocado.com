/**
 * Eliminar Publicación
 * 
 * Este script permite a un usuario eliminar una publicación de la base de datos.
 * 
 * Funcionalidades:
 * - Verifica si el usuario está logueado.
 * - Verifica si el ID de la publicación fue pasado por GET y es numérico.
 * - Verifica si la publicación existe y pertenece al usuario logueado.
 * - Elimina la publicación si todas las verificaciones son correctas.
 * - Redirige al perfil del usuario con un mensaje de éxito si la eliminación es exitosa.
 * 
 * Requiere:
 * - Una conexión a la base de datos establecida en 'conexion.php'.
 * 
 * Variables de sesión:
 * - $_SESSION['usuario_id']: ID del usuario logueado.
 * 
 * Parámetros GET:
 * - id: ID de la publicación a eliminar.
 * 
 * Redirecciones:
 * - Redirige a 'login.php' si el usuario no está logueado.
 * - Redirige a 'perfil.php' con un mensaje de éxito si la publicación es eliminada.
 * 
 * Mensajes de error:
 * - "ID de publicación no válido." si el ID no es pasado por GET o no es numérico.
 * - "No tienes permiso para eliminar esta publicación." si la publicación no pertenece al usuario.
 * - "Error al eliminar la publicación." si ocurre un error durante la eliminación.
 */
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
