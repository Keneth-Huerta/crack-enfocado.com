<?php
session_start();
require 'conexion.php'; // Asegúrate de tener el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $estrellas = intval($_POST['estrellas']);
    $comentario = htmlspecialchars($_POST['comentario']);

    // Verificar si ya existe una calificación
    $check_query = "SELECT id FROM calificaciones 
                   WHERE producto_id = ? AND usuario_id = ?";
    $stmt = mysqli_prepare($enlace, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $producto_id, $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Actualizar calificación existente
        $query = "UPDATE calificaciones 
                 SET estrellas = ?, comentario = ? 
                 WHERE producto_id = ? AND usuario_id = ?";
    } else {
        // Insertar nueva calificación
        $query = "INSERT INTO calificaciones (producto_id, usuario_id, estrellas, comentario) 
                 VALUES (?, ?, ?, ?)";
    }

    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $producto_id, $usuario_id, $estrellas, $comentario);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['mensaje'] = "Calificación guardada exitosamente.";
        $_SESSION['mensaje_tipo'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al guardar la calificación.";
        $_SESSION['mensaje_tipo'] = "danger";
    }
}

header("Location: ventas.php");
exit();
