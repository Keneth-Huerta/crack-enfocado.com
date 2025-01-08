<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

if (!isset($_POST['publicacion_id']) || !isset($_POST['contenido']) || empty(trim($_POST['contenido']))) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_POST['publicacion_id'];
$contenido = trim($_POST['contenido']);

try {
    // Insertar el comentario
    $stmt = $enlace->prepare("INSERT INTO comentarios (usuario_id, publicacion_id, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $usuario_id, $publicacion_id, $contenido);

    if ($stmt->execute()) {
        // Obtener los datos del comentario recién creado
        $comentario_id = $stmt->insert_id;

        $stmt = $enlace->prepare("
            SELECT c.*, p.nombre, p.apellido, p.foto_perfil, 
                   DATE_FORMAT(c.fecha_comentario, '%d/%m/%Y %H:%i') as fecha_formateada
            FROM comentarios c
            JOIN perfiles p ON c.usuario_id = p.usuario_id
            WHERE c.id_comentario = ?
        ");
        $stmt->bind_param("i", $comentario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $comentario = $resultado->fetch_assoc();

        // Preparar la respuesta
        $response = [
            'success' => true,
            'comment' => [
                'id' => $comentario_id,
                'contenido' => htmlspecialchars($comentario['contenido']),
                'nombre' => htmlspecialchars($comentario['nombre']),
                'apellido' => htmlspecialchars($comentario['apellido']),
                'foto_perfil' => htmlspecialchars($comentario['foto_perfil']),
                'fecha_comentario' => $comentario['fecha_formateada']
            ]
        ];

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar el comentario']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error en el servidor']);
}
