<?php
session_start();
// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Si no hay sesión, redirigir a login
    exit();
}

$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$usuario_id = $_SESSION['usuario_id']; // Obtener el ID del usuario desde la sesión

// Obtener los datos actuales del perfil
$query = "SELECT nombre, apellido, boleta, carrera, semestre, foto_perfil, foto_portada, informacion_extra FROM perfiles WHERE usuario_id = ?";
if ($stmt = mysqli_prepare($enlace, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre, $apellido, $boleta, $carrera, $semestre, $foto_perfil, $foto_portada, $informacion_extra);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Si el formulario es enviado, actualizar el perfil
if (isset($_POST['guardar'])) {
    $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($enlace, $_POST['apellido']);
    $carrera = mysqli_real_escape_string($enlace, $_POST['carrera']);
    $semestre = mysqli_real_escape_string($enlace, $_POST['semestre']);
    $informacion_extra = mysqli_real_escape_string($enlace, $_POST['informacion_extra']);

    // Subir imagen de perfil si se selecciona una nueva
    if ($_FILES['foto_perfil']['name']) {
        $foto_perfil = "uploads/" . basename($_FILES['foto_perfil']['name']);
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_perfil);
    } else {
        $foto_perfil = $foto_perfil; // Mantener la foto actual si no se sube una nueva
    }

    // Actualizar los datos del perfil
    $updateQuery = "UPDATE perfiles SET nombre = ?, apellido = ?, carrera = ?, semestre = ?, foto_perfil = ?, foto_portada = ?, informacion_extra = ? WHERE usuario_id = ?";
    $stmt = mysqli_prepare($enlace, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssssi", $nombre, $apellido, $carrera, $semestre, $foto_perfil, $foto_portada, $informacion_extra, $usuario_id);

    if (mysqli_stmt_execute($stmt)) {
        echo '<script>alert("Perfil actualizado correctamente"); window.location.href = "perfil.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar el perfil");</script>';
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($enlace);
?>
