<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php'; // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php'); // Redirigir si no está logueado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $contenido_comentario = $_POST['contenido_comentario'];
    $publicacion_id = $_POST['publicacion_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Insertar el comentario en la base de datos
    $stmt = $enlace->prepare("INSERT INTO comentarios (usuario_id, publicacion_id, contenido) 
                              VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $usuario_id, $publicacion_id, $contenido_comentario);
    $stmt->execute();

    // Redirigir al usuario a la página de publicaciones
    header('Location: publicaciones.php');
    exit();
}
