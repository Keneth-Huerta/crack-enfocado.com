<?php
// Configuración de la base de datos
$servidor = "fdb1029.awardspace.net";
$usuario = "4565088_usuarios";
$clave = "alexander1234";
$baseDeDatos = "4565088_usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Iniciar sesión
session_start();

// Validar entrada del formulario
if (isset($_POST['correo'], $_POST['contra'])) {
    $correo = $_POST['correo'];
    $contra = $_POST['contra'];

    // Preparar y ejecutar consulta segura
    $query = "SELECT * FROM registro WHERE correo = ?";
    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Verificar la contraseña (si está cifrada)
        if ($contra=$fila['contra']) {
            // Guardar datos en sesión
            $_SESSION['usuario'] = $fila['correo'];
            header("Location: ../usuario.html");
            exit();
        } else {
            echo '<script>
                alert("Usuario o contraseña inválidos");
                location.href="../formulario2.html";
            </script>';
        }
    } else {
        echo '<script>
            alert("Usuario o contraseña inválidos");
            location.href="../formulario2.html";
        </script>';
    }

    mysqli_stmt_close($stmt);
} else {
    echo '<script>
        alert("Por favor completa todos los campos");
        location.href="../formulario2.html";
    </script>';
}

// Cerrar conexión
mysqli_close($enlace);
?>

