/**
 * Este script maneja la adición de comentarios a una publicación.
 * 
 * Requiere que el usuario esté autenticado y que se envíen los datos necesarios
 * a través de una solicitud JSON o POST.
 * 
 * Dependencias:
 * - conexion.php: Archivo que establece la conexión a la base de datos.
 * 
 * Flujo del script:
 * 1. Inicia la sesión y verifica si el usuario está autenticado.
 * 2. Obtiene los datos de la solicitud (JSON o POST).
 * 3. Valida que los datos necesarios estén presentes.
 * 4. Inserta el comentario en la base de datos.
 * 5. Recupera y devuelve los datos del comentario recién creado en formato JSON.
 * 
 * Respuestas JSON:
 * - Éxito: 
 *   {
 *     "success": true,
 *     "comment": {
 *       "id_comentario": int,
 *       "usuario_id": int,
 *       "publicacion_id": int,
 *       "contenido": string,
 *       "fecha_comentario": string (formato "d/m/Y H:i"),
 *       "nombre": string,
 *       "apellido": string,
 *       "foto_perfil": string
 *     }
 *   }
 * - Error:
 *   {
 *     "success": false,
 *     "error": string
 *   }
 * 
 * @throws Exception Si ocurre un error al insertar el comentario o al procesar la solicitud.
 */
<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

// Obtener datos de la solicitud
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Si no hay datos JSON, intentar obtener datos POST normales
if (empty($data)) {
    $publicacion_id = $_POST['publicacion_id'] ?? '';
    $contenido = trim($_POST['contenido'] ?? '');
} else {
    $publicacion_id = $data['publicacion_id'] ?? '';
    $contenido = trim($data['contenido'] ?? '');
}

$usuario_id = $_SESSION['usuario_id'];

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
