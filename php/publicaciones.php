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

// Obtener las publicaciones
$stmt = $enlace->prepare("SELECT publicaciones.*, perfiles.foto_perfil, perfiles.nombre 
                          FROM publicaciones 
                          JOIN perfiles ON publicaciones.usuario_id = perfiles.usuario_id
                          ORDER BY publicaciones.fecha_publicada DESC");
$stmt->execute();
$publicaciones = $stmt->get_result();

// Mostrar publicaciones
while ($publicacion = $publicaciones->fetch_assoc()) {
    echo '<div class="post-item">';

    // Contenido del encabezado (usuario y avatar)
    echo '<div class="post-header">';
    $foto_perfil = $publicacion['foto_perfil'] ? htmlspecialchars($publicacion['foto_perfil']) : 'default-profile.jpg';
    echo '<div class="post-avatar">';
    echo '<img src="' . $foto_perfil . '" alt="Foto de perfil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">';
    echo '</div>';
    echo '<div class="post-username">' . htmlspecialchars($publicacion['nombre']) . '</div>';
    echo '</div>';

    // Contenido de la publicación
    echo '<div class="post-content">' . htmlspecialchars($publicacion['contenido']) . '</div>';

    // Imagen de la publicación (si existe)
    if (!empty($publicacion['imagen'])) {
        echo '<img src="' . htmlspecialchars($publicacion['imagen']) . '" alt="Imagen de publicación">';
    }

    // Mostrar comentarios
    echo '<div class="comments-section">';

    // Obtener los comentarios de esta publicación
    $stmt_comentarios = $enlace->prepare("SELECT comentarios.*, usuarios.nombre AS usuario_nombre 
                                          FROM comentarios 
                                          JOIN usuarios ON comentarios.usuario_id = usuarios.id_usuario 
                                          WHERE comentarios.publicacion_id = ? 
                                          ORDER BY comentarios.fecha_comentario ASC");
    $stmt_comentarios->bind_param("i", $publicacion['id_publicacion']);
    $stmt_comentarios->execute();
    $comentarios = $stmt_comentarios->get_result();

    // Mostrar cada comentario
    while ($comentario = $comentarios->fetch_assoc()) {
        echo '<div class="comment-item">';
        echo '<strong>' . htmlspecialchars($comentario['usuario_nombre']) . ':</strong>';
        echo '<p>' . htmlspecialchars($comentario['contenido']) . '</p>';
        echo '</div>';
    }

    // Formulario para agregar un comentario
    echo '<form action="agregar_comentario.php" method="POST">';
    echo '<input type="hidden" name="publicacion_id" value="' . $publicacion['id_publicacion'] . '">';
    echo '<textarea name="contenido_comentario" placeholder="Escribe un comentario..." required></textarea>';
    echo '<button type="submit">Comentar</button>';
    echo '</form>';

    echo '</div>'; // Cierre de comentarios
    echo '</div>'; // Cierre de post-item
}
?>
