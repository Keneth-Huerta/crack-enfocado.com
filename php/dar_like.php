<?php
session_start();
include 'conexion.php';  // Conexión a la base de datos

if (isset($_POST['like']) && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $publicacion_id = $_POST['publicacion_id'];

    // Verificar si el usuario ya ha dado like a esta publicación
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
    $stmt->execute([$usuario_id, $publicacion_id]);
    $like = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$like) {
        // Si no ha dado like, insertamos el like
        $stmt = $pdo->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $publicacion_id]);

        // Incrementamos la cantidad de me gusta en la publicación
        $stmt = $pdo->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?");
        $stmt->execute([$publicacion_id]);
    }

    // Redirigir de vuelta a la página de publicaciones
    header("Location: publicaciones.php");
    exit();
} else {
    // Si el usuario no está logueado, redirigimos al login
    header("Location: index.html");
    exit();
}
