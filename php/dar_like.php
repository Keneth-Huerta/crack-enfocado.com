<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; // Asegúrate de incluir el archivo de conexión

// Verificar que el usuario esté logueado
if (isset($_SESSION['usuario_id'])) {
    // Obtener el ID de la publicación y el ID del usuario
    $usuario_id = $_SESSION['usuario_id']; 
    $id_publicacion = $_POST['id_publicacion'];  // Suponiendo que el ID de la publicación se recibe por POST
    
    // Comprobar si el usuario ya ha dado like (puedes crear una tabla de "likes" para manejar esto de forma más avanzada)
    // Suponiendo que no tienes una tabla de "likes", incrementaremos directamente la cantidad en la tabla de publicaciones
    $query = "UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?";
    
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id_publicacion);  // Vinculamos el parámetro de la consulta
        if (mysqli_stmt_execute($stmt)) {
            echo "Like registrado exitosamente!";
            header("Location: publicaciones.php");
        } else {
            echo "Error al registrar el like.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la preparación de la consulta.";
    }
} else {
    echo "Debe iniciar sesión para dar un like.";
}

?>
