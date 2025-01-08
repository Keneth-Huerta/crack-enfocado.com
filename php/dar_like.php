<?php
// dar_like.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

// Función para crear notificación de like
function crearNotificacionLike($enlace, $publicacion_id, $usuario_que_dio_like)
{
    // Obtener el usuario dueño de la publicación
    $stmt = $enlace->prepare("SELECT usuario_id, titulo FROM publicaciones WHERE id_publicacion = ?");
    $stmt->bind_param("i", $publicacion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $publicacion = $result->fetch_assoc();

    // No crear notificación si el usuario da like a su propia publicación
    if ($publicacion['usuario_id'] == $usuario_que_dio_like) {
        return;
    }

    // Obtener nombre del usuario que dio like
    $stmt = $enlace->prepare("SELECT nombre, apellido FROM perfiles WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_que_dio_like);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    $nombre_completo = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $mensaje = $nombre_completo . " le dio me gusta a tu publicación '" . substr($publicacion['titulo'], 0, 30) . "'";

    // Insertar notificación
    $stmt = $enlace->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje, referencia_id, fecha) 
                             VALUES (?, 'like', ?, ?, NOW())");
    $stmt->bind_param("isi", $publicacion['usuario_id'], $mensaje, $publicacion_id);
    $stmt->execute();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "error|No autorizado";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_publicacion = $_POST['id_publicacion'];

    // Verificar si ya existe el like
    $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
    $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insertar like
        $stmt = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();

        // Actualizar contador
        $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();

        // Crear notificación
        crearNotificacionLike($enlace, $id_publicacion, $_SESSION['usuario_id']);

        // Obtener nuevo contador
        $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo "liked|" . $row['cantidad_megusta'];
    } else {
        // Eliminar like y su notificación correspondiente
        $stmt = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();

        // Actualizar contador
        $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta - 1 WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();

        // Obtener nuevo contador
        $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo "unliked|" . $row['cantidad_megusta'];
    }
}
