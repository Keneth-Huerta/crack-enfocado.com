<?php
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Validación y sanitización de los datos del formulario
if (isset($_POST['registrar'])) {
    $usuario = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($enlace, $_POST['apellido']);
    $boleta = mysqli_real_escape_string($enlace, $_POST['boleta']);
    $correo = mysqli_real_escape_string($enlace, $_POST['correo']);
    $contraseña = $_POST['contraseña'];

    // Cifrar la contraseña
    $contraseñaCifrada = password_hash($contraseña, PASSWORD_DEFAULT);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Preparar la consulta SQL con parámetros
    $insertarDatos = "INSERT INTO registro (nombre, apellido, boleta, correo, contraseña) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($enlace, $insertarDatos);
    mysqli_stmt_bind_param($stmt, "sssss", $usuario, $apellido, $boleta, $correo, $contraseñaCifrada);

    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        echo ' <script>
            location.href="../index.html"; // Redirigir al índice
        </script>';
    } else {
        echo "Error al registrar: " . mysqli_error($enlace);
    }

    // Cerrar la declaración y la conexión
    mysqli_stmt_close($stmt);
    mysqli_close($enlace);
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
