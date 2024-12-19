<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; // Asegúrate de incluir el archivo de conexión

// Verificar que el usuario esté logueado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];  // ID del usuario
    $id_publicacion = $_POST['id_publicacion'];  // ID de la publicación

    // Verificar si el usuario ya ha dado "Me gusta" a esta publicación
    $check_like_query = "SELECT * FROM likes WHERE usuario_id = ? AND id_publicacion = ?";
    
    if ($stmt = mysqli_prepare($enlace, $check_like_query)) {
        mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $id_publicacion);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Si ya existe un registro en la tabla de likes, no se permite dar otro "Me gusta"
        if (mysqli_num_rows($result) > 0) {
            echo "Ya has dado 'Me gusta' a esta publicación.";
        } else {
            // Si no existe, insertamos el like en la tabla de likes
            $insert_like_query = "INSERT INTO likes (usuario_id, id_publicacion) VALUES (?, ?)";
            
            if ($stmt = mysqli_prepare($enlace, $insert_like_query)) {
                mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $id_publicacion);
                if (mysqli_stmt_execute($stmt)) {
                    // Ahora incrementamos el contador de "Me gusta" en la tabla de publicaciones
                    $update_like_query = "UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?";
                    
                    if ($stmt = mysqli_prepare($enlace, $update_like_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $id_publicacion);
                        mysqli_stmt_execute($stmt);
                        echo "Like registrado exitosamente!";
                        header("Location: publicaciones.phpf");
                    } else {
                        echo "Error al actualizar los 'Me gusta'.";
                    }
                } else {
                    echo "Error al insertar el like.";
                }
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "Debe iniciar sesión para dar un like.";
}

?>
