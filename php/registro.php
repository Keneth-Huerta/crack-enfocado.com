
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Validar datos del formulario
if (isset($_POST['registrar'])) {
    $username = mysqli_real_escape_string($enlace, $_POST['username']);
    $correo = mysqli_real_escape_string($enlace, $_POST['correo']);
    $boleta = mysqli_real_escape_string($enlace, $_POST['boleta']);
    $contraseña = $_POST['contraseña'];

    // Cifrar la contraseña
    $contraseñaCifrada = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertar usuario en la tabla `usuarios`
    $insertarUsuario = "INSERT INTO usuarios (username, correo, boleta, contraseña) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($enlace, $insertarUsuario);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $correo, $boleta, $contraseñaCifrada);

    if (mysqli_stmt_execute($stmt)) {
        // Obtener el ID del usuario
        $usuario_id = mysqli_insert_id($enlace);

        // Iniciar sesión automáticamente
        $_SESSION['usuario'] = $correo;
        $_SESSION['usuario_id'] = $usuario_id;

        // Redirigir a la página de edición del perfil
        echo '<script>
            window.location.href = "editar_perfil.php"; // Redirigir para completar los datos
        </script>';
    } else {
        echo "Error al registrar: " . mysqli_error($enlace);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($enlace);
}
?>
