<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $publicacion_id = $_POST['publicacion_id'] ?? '';
    $contenido = trim($_POST['contenido'] ?? '');

    if (empty($contenido) || empty($publicacion_id)) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    try {
        // Insertar el comentario
        $stmt = $enlace->prepare("INSERT INTO comentarios (usuario_id, publicacion_id, contenido) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $usuario_id, $publicacion_id, $contenido);

        if ($stmt->execute()) {
            // Obtener los datos del comentario recién creado
            $comment_id = $stmt->insert_id;

            $query = "SELECT c.*, p.nombre, p.apellido, p.foto_perfil 
                     FROM comentarios c 
                     JOIN perfiles p ON c.usuario_id = p.usuario_id 
                     WHERE c.id_comentario = ?";

            $stmt = $enlace->prepare($query);
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $comment = $result->fetch_assoc();

            // Formatear la fecha
            $comment['fecha_comentario'] = date("d/m/Y H:i", strtotime($comment['fecha_comentario']));

            echo json_encode([
                'success' => true,
                'comment' => $comment
            ]);
        } else {
            throw new Exception('Error al insertar el comentario');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
