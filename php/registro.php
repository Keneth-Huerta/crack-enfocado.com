<?php
// Configuración de la base de datos
$servidor = "localhost"; // Cambiar por tu host
$usuarioBD = "u288355303_Keneth"; // Cambiar por tu usuario de la DB
$claveBD = ""; // Cambiar por tu contraseña de la DB
$baseDeDatos = "u288355303_Usuarios"; // Cambiar por tu nombre de la base de datos

// Conexión a la base de datos
$conexion = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);

// Verificar la conexión
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Escapar y sanitizar datos recibidos
function escapar_entrada($dato, $conexion) {
    return mysqli_real_escape_string($conexion, trim($dato));
}

// Procesar datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = escapar_entrada($_POST['nombre'], $conexion);
    $apellido = escapar_entrada($_POST['apellido'], $conexion);
    $boleta = escapar_entrada($_POST['boleta'], $conexion);
    $correo = escapar_entrada($_POST['correo'], $conexion);
    $contrasena = $_POST['contrasena'];

    // Validaciones adicionales
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Correo inválido.";
        exit;
    }

    if (strlen($contrasena) < 6) {
        echo "La contraseña debe tener al menos 6 caracteres.";
        exit;
    }

    // Cifrar la contraseña
    $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar los datos en la base de datos
    $query = "INSERT INTO registro (nombre, apellido, boleta, correo, contrasena) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $nombre, $apellido, $boleta, $correo, $contrasena_cifrada);
        if (mysqli_stmt_execute($stmt)) {
            echo "Registro exitoso.";
            header("Location: ../index.html"); // Redirigir al usuario
            exit();
        } else {
            echo "Error al guardar los datos: " . mysqli_error($conexion);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la consulta: " . mysqli_error($conexion);
    }
}

// Cerrar conexión
mysqli_close($conexion);
?>