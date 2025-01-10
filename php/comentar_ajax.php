<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    $response = array();

    $producto_id = intval($_POST['producto_id']);
    $usuario_id = $_SESSION['usuario_id'];
    $contenido = htmlspecialchars($_POST['contenido']);

    $query = "INSERT INTO comentarios (usuario_id, publicacion_id, contenido) 
              VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "iis", $usuario_id, $producto_id, $contenido);

    if (mysqli_stmt_execute($stmt)) {
        // Obtener información del usuario que comentó
        $user_query = "SELECT u.username, p.foto_perfil, p.nombre, p.apellido 
                      FROM usuarios u 
                      JOIN perfiles p ON u.id = p.usuario_id 
                      WHERE u.id = ?";
        $stmt = mysqli_prepare($enlace, $user_query);
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        $user_result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($user_result);

        $response['success'] = true;
        $response['comentario'] = array(
            'id' => mysqli_insert_id($enlace),
            'contenido' => $contenido,
            'fecha' => date('Y-m-d H:i:s'),
            'usuario' => $user_data['nombre'] . ' ' . $user_data['apellido'],
            'foto_perfil' => $user_data['foto_perfil'] ?? '../media/user.png',
            'es_propietario' => true
        );
    } else {
        $response['success'] = false;
        $response['mensaje'] = "Error al publicar el comentario";
    }

    echo json_encode($response);
    exit();
}
