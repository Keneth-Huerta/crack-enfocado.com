<?php
// Configuración de la base de datos
$servor = "localhost";
$usuarip = "u288355303_Keneth";
$clave = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servor, $usuarip, $clave, $baseDeDatos);

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
        if ($contra === $fila['contra']) {
            // Guardar datos en sesión
            $_SESSION['usuario'] = $fila['correo'];
            header("Location: ../usuario.html");
            exit();
        } else {
            echo '<script>
                alert("Usuario o contraseña inválidos");
                location.href="../index.html";
            </script>';
        }
    } else {
        echo '<script>
            alert("Usuario o contraseña inválidos");
            location.href="../index.html";
        </script>';
    }

    mysqli_stmt_close($stmt);
} else {
    echo '<script>
        alert("Por favor completa todos los campos");
        location.href="../index.html";
    </script>';
}

// Cerrar conexión
mysqli_close($enlace);
?>
