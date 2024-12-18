<?php
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

// Iniciar sesión
session_start();

// Validar si el formulario ha sido enviado
if (isset($_POST['login'])) {
    $login_input = $_POST['login_input'];  // Nombre de usuario o correo
    $contraseña = $_POST['contraseña'];

    // Comprobar si el input es un correo electrónico o un nombre de usuario
    if (filter_var($login_input, FILTER_VALIDATE_EMAIL)) {
        // Si es correo electrónico
        $query = "SELECT * FROM usuarios WHERE correo = ?";
    } else {
        // Si es nombre de usuario
        $query = "SELECT * FROM usuarios WHERE username = ?";
    }

    // Preparar la consulta y ejecutar
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $login_input);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($usuario = mysqli_fetch_assoc($resultado)) {
            // Verificar la contraseña
            if (password_verify($contraseña, $usuario['contraseña'])) {
                // Si la contraseña es correcta, iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];  // Guardar el ID de usuario en la sesión
                $_SESSION['usuario'] = $usuario['username'];  // Guardar el nombre de usuario en la sesión
                header("Location: usuario.php");  // Redirigir a la página de perfil o inicio
                exit();
            } else {
                echo '<script>alert("Contraseña incorrecta.");</script>';
            }
        } else {
            echo '<script>alert("Usuario no encontrado.");</script>';
        }
        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("Error al procesar la solicitud. Intenta de nuevo.");</script>';
    }
}

mysqli_close($enlace);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>

<div class="form-container">
    <h1>Iniciar Sesión</h1>
    <form action="login.php" method="POST">
        <div class="form-group">
            <input type="text" name="login_input" required placeholder="Nombre de Usuario o Correo" />
        </div>
        <div class="form-group">
            <input type="password" name="contraseña" required placeholder="Contraseña" />
        </div>
        <button type="submit" name="login">Iniciar Sesión</button>
    </form>
</div>

</body>
</html>
