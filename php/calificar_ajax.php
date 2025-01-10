<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    $response = array();

    $producto_id = intval($_POST['producto_id']);
    $usuario_id = $_SESSION['usuario_id'];
    $estrellas = intval($_POST['estrellas']);

    // Verificar si ya existe una calificación
    $check_query = "SELECT id FROM calificaciones 
                   WHERE producto_id = ? AND usuario_id = ?";
    $stmt = mysqli_prepare($enlace, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $producto_id, $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $query = "UPDATE calificaciones 
                 SET estrellas = ? 
                 WHERE producto_id = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "iii", $estrellas, $producto_id, $usuario_id);
    } else {
        $query = "INSERT INTO calificaciones (producto_id, usuario_id, estrellas) 
                 VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "iii", $producto_id, $usuario_id, $estrellas);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Obtener el nuevo promedio de calificaciones
        $avg_query = "SELECT AVG(estrellas) as promedio, COUNT(*) as total 
                     FROM calificaciones 
                     WHERE producto_id = ?";
        $stmt = mysqli_prepare($enlace, $avg_query);
        mysqli_stmt_bind_param($stmt, "i", $producto_id);
        mysqli_stmt_execute($stmt);
        $avg_result = mysqli_stmt_get_result($stmt);
        $rating_data = mysqli_fetch_assoc($avg_result);

        $response['success'] = true;
        $response['promedio'] = round($rating_data['promedio'], 1);
        $response['total'] = $rating_data['total'];
        $response['mensaje'] = "Calificación guardada exitosamente";
    } else {
        $response['success'] = false;
        $response['mensaje'] = "Error al guardar la calificación";
    }

    echo json_encode($response);
    exit();
}
