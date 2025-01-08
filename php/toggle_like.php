
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
