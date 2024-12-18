<?php
// Conexión a la base de datos
$servor="localhost";
$usuarip="u288355303_Keneth";
$clave="1420Genio.";
$baseDeDatos="u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servor, $usuarip, $clave, $baseDeDatos);

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
?>
