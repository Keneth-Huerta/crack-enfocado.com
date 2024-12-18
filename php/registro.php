<?php
$host = 'localhost';
$dbname = 'u288355303_Usuarios';
$username = 'u288355303_Keneth';
$password = '1420Genio.';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$enlace = mysqli_connect($host, $dbname, $username, $password);
if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
} else {
    echo "Conexión exitosa";
}

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
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . mysqli_error($enlace));
    }

    mysqli_stmt_bind_param($stmt, "sssss", $usuario, $apellido, $boleta, $correo, $contraseñaCifrada);

    if (!mysqli_stmt_execute($stmt)) {
        die("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
    }

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
