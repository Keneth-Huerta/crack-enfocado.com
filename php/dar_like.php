<?php
session_start();
include 'conexion.php'; // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el ID de la publicación desde el formulario
    $id_publicacion = $_POST['id_publicacion'];

    // Verificar si el usuario ya ha dado "Me gusta" a esta publicación
    $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND id_publicacion = ?");
    $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
    $stmt->execute();
    $like_check = $stmt->get_result();

    if (mysqli_num_rows($like_check) == 0) {
        // Si no ha dado "Me gusta", insertamos el like
        $stmt_insert = $enlace->prepare("INSERT INTO likes (usuario_id, id_publicacion) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt_insert->execute();

        // Actualizamos el contador de "Me gusta" en la publicación
        $stmt_update = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE publicacion_id = ?");
        $stmt_update->bind_param("i", $id_publicacion);
        $stmt_update->execute();
    }

    // Redirigir de nuevo a la página de publicaciones
    header('Location: publicaciones.php');
    exit();
}
?>
