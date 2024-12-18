<?php
include 'db.php'; // Mueve la conexión a un archivo separado para reusabilidad

// Función para escapar y validar datos
function escapar_entrada($entrada, $enlace) {
    return mysqli_real_escape_string($enlace, trim($entrada));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $usuario = escapar_entrada($_POST['nombre'], $enlace);
    $apellido = escapar_entrada($_POST['apellido'], $enlace);
    $boleta = escapar_entrada($_POST['boleta'], $enlace);
    $correo = escapar_entrada($_POST['correo'], $enlace);
    $contrasena = $_POST['contrasena'];

    // Validación del correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Correo inválido."); location.href="../crearCuenta.html";</script>';
        exit();
    }

    // Validación de contraseña
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', $contrasena)) {
        echo '<script>alert("La contraseña debe tener al menos 6 caracteres, incluyendo letras y números."); location.href="../crearCuenta.html";</script>';
        exit();
    }

    // Verificar si el correo ya existe
    $queryVerificarCorreo = "SELECT 1 FROM registro WHERE correo = ?";
    if ($stmt = mysqli_prepare($enlace, $queryVerificarCorreo)) {
        mysqli_stmt_bind_param($stmt, "s", $correo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo '<script>alert("El correo ya está registrado."); location.href="../crearCuenta.html";</script>';
            exit();
        }
        mysqli_stmt_close($stmt);
    }

    // Insertar los datos
    $contraseñaCifrada = password_hash($contrasena, PASSWORD_DEFAULT);
    $queryInsertar = "INSERT INTO registro (nombre, apellido, boleta, correo, contraseña) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($enlace, $queryInsertar)) {
        mysqli_stmt_bind_param($stmt, "ssiss", $usuario, $apellido, $boleta, $correo, $contraseñaCifrada);
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("Registro exitoso."); location.href="../index.html";</script>';
            exit();
        } else {
            error_log("Error al registrar: " . mysqli_error($enlace));
            echo '<script>alert("Error al registrar. Intente nuevamente."); location.href="../crearCuenta.html";</script>';
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error al preparar la consulta: " . mysqli_error($enlace));
        echo '<script>alert("Error en el sistema. Intente nuevamente."); location.href="../crearCuenta.html";</script>';
    }

    mysqli_close($enlace);
}
?>

