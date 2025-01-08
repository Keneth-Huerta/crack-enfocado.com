
<?php
// comentar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

// Función para crear notificación de comentario
function crearNotificacionComentario($enlace, $publicacion_id, $usuario_que_comento, $contenido_comentario)
{
    // Obtener el usuario dueño de la publicación
    $stmt = $enlace->prepare("SELECT usuario_id, contenido FROM publicaciones WHERE id_publicacion = ?");
    $stmt->bind_param("i", $publicacion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $publicacion = $result->fetch_assoc();

    // No crear notificación si el usuario comenta su propia publicación
    if ($publicacion['usuario_id'] == $usuario_que_comento) {
        return;
    }

    // Obtener nombre del usuario que comentó
    $stmt = $enlace->prepare("SELECT nombre, apellido FROM perfiles WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_que_comento);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    $nombre_completo = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $mensaje = $nombre_completo . " comentó en tu publicación '" . substr($publicacion['contenido'], 0, 30) . "'";

    // Insertar notificación
    $stmt = $enlace->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje, referencia_id, fecha) 
                             VALUES (?, 'comentario', ?, ?, NOW())");
    $stmt->bind_param("isi", $publicacion['usuario_id'], $mensaje, $publicacion_id);
    $stmt->execute();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../secion.php');
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

    if ($stmt->execute()) {
        // Crear notificación
        crearNotificacionComentario($enlace, $publicacion_id, $usuario_id, $contenido_comentario);

        // Redirigir al usuario a la página de publicaciones
        header('Location: publicaciones.php');
        exit();
    }
}
?>