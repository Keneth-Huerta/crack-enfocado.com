/**
 * This script handles user registration by processing form data, 
 * inserting the user into the database, and starting a session.
 * 
 * - Starts a session if not already started.
 * - Connects to the MySQL database using provided credentials.
 * - Validates and sanitizes form data.
 * - Hashes the user's password.
 * - Inserts the new user into the `usuarios` table.
 * - Starts a session for the new user and redirects to the profile edit page.
 * 
 * @file /d:/crack-enfocado.com/php/registro.php
 * 
 * @param string $servidor The database server address.
 * @param string $usuarioBD The database username.
 * @param string $claveBD The database password.
 * @param string $baseDeDatos The database name.
 * 
 * @param string $_POST['username'] The username from the registration form.
 * @param string $_POST['correo'] The email from the registration form.
 * @param string $_POST['boleta'] The boleta from the registration form.
 * @param string $_POST['contraseña'] The password from the registration form.
 * 
 * @return void
 * 
 * @throws Exception If the database connection fails.
 * @throws Exception If the user registration fails.
 */
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
