/**
 * This script toggles the like status of a publication for a logged-in user.
 * 
 * It expects the following:
 * - A valid session with 'usuario_id' set.
 * - A POST request with 'publicacion_id' parameter.
 * 
 * The script performs the following actions:
 * 1. Checks if the user is authenticated. If not, returns a 401 error.
 * 2. Checks if the 'publicacion_id' is provided. If not, returns a 400 error.
 * 3. Verifies if the user has already liked the publication.
 *    - If the like exists, it removes the like.
 *    - If the like does not exist, it adds a like.
 * 4. Retrieves the updated count of likes for the publication.
 * 5. Returns a JSON response with the success status, the new like status, and the updated like count.
 * 
 * JSON Response:
 * - success: boolean indicating the operation success.
 * - liked: boolean indicating if the publication is liked by the user after the operation.
 * - likes_count: integer representing the total number of likes for the publication.
 * 
 * HTTP Status Codes:
 * - 200: Success
 * - 400: Bad Request (if 'publicacion_id' is not provided)
 * - 401: Unauthorized (if the user is not authenticated)
 */
<?php
session_start();
include 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

if (!isset($_POST['publicacion_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de publicación no proporcionado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_POST['publicacion_id'];

// Verificar si ya existe el like
$stmt = $enlace->prepare("SELECT like_id FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
$stmt->bind_param("ii", $usuario_id, $publicacion_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si existe, eliminar el like
    $stmt = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
    $stmt->bind_param("ii", $usuario_id, $publicacion_id);
    $stmt->execute();
    $liked = false;
} else {
    // Si no existe, crear el like
    $stmt = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $usuario_id, $publicacion_id);
    $stmt->execute();
    $liked = true;
}

// Obtener el nuevo conteo de likes
$stmt = $enlace->prepare("SELECT COUNT(*) as likes_count FROM likes WHERE publicacion_id = ?");
$stmt->bind_param("i", $publicacion_id);
$stmt->execute();
$result = $stmt->get_result();
$likes_count = $result->fetch_assoc()['likes_count'];

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'likes_count' => $likes_count
]);
