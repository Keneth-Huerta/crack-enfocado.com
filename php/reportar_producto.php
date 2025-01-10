<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $motivo = htmlspecialchars($_POST['motivo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);

    $query = "INSERT INTO reportes (producto_id, usuario_id, motivo, descripcion) 
              VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $producto_id, $usuario_id, $motivo, $descripcion);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['mensaje'] = "Reporte enviado exitosamente.";
        $_SESSION['mensaje_tipo'] = "success";

        // Verificar si el producto tiene muchos reportes
        $query_count = "SELECT COUNT(*) as total_reportes 
                       FROM reportes 
                       WHERE producto_id = ? AND estado = 'pendiente'";
        $stmt = mysqli_prepare($enlace, $query_count);
        mysqli_stmt_bind_param($stmt, "i", $producto_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Si hay más de 5 reportes, ocultar el producto
        if ($row['total_reportes'] >= 5) {
            $update_query = "UPDATE productos 
                           SET estado = 'oculto' 
                           WHERE idProducto = ?";
            $stmt = mysqli_prepare($enlace, $update_query);
            mysqli_stmt_bind_param($stmt, "i", $producto_id);
            mysqli_stmt_execute($stmt);
        }
    } else {
        $_SESSION['mensaje'] = "Error al enviar el reporte.";
        $_SESSION['mensaje_tipo'] = "danger";
    }
}

header("Location: ventas.php");
exit();
