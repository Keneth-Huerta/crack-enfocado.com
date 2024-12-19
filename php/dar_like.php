<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo 'error';
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id_publicacion = $_POST['id_publicacion'];

// Verificar si el usuario ya dio like
$stmt_check = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
$stmt_check->bind_param("ii", $usuario_id, $id_publicacion);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Si ya le dio like, eliminar el like
    $stmt_delete = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
    $stmt_delete->bind_param("ii", $usuario_id, $id_publicacion);
    $stmt_delete->execute();
    $status = 'unliked';
} else {
    // Si no le dio like, agregar el like
    $stmt_insert = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $usuario_id, $id_publicacion);
    $stmt_insert->execute();
    $status = 'liked';
}

// Actualizar la cantidad de likes
$stmt_count = $enlace->prepare("SELECT COUNT(*) AS cantidad_megusta FROM likes WHERE publicacion_id = ?");
$stmt_count->bind_param("i", $id_publicacion);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$row = $count_result->fetch_assoc();
$newCount = $row['cantidad_megusta'];

// Devolver el estado y el nuevo contador
echo "$status|$newCount";
?>
