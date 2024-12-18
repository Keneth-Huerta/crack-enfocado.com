<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);

// Verificar la conexión
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Función para escapar entradas
function escapar_entrada($entrada, $enlace) {
    return mysqli_real_escape_string($enlace, trim($entrada));
}

// Validación y sanitización de los datos del formulario
if (isset($_POST['registrar'])) {
    $usuario = escapar_entrada($_POST['nombre'], $enlace);
    $apellido = escapar_entrada($_POST['apellido'], $enlace);
    $boleta = escapar_entrada($_POST['boleta'], $enlace);
    $correo = escapar_entrada($_POST['correo'], $enlace);
    $contrasena = $_POST['contrasena'];

    // Validación del correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Por favor, ingresa un correo válido."); location.href="../registro.html";</script>';
        exit();
    }

    // Validación de la contraseña (mínimo 6 caracteres)
    if (strlen($contrasena) < 6) {
        echo '<script>alert("La contraseña debe tener al menos 6 caracteres."); location.href="../registro.html";</script>';
        exit();
    }

    // Cifrar la contraseña
    $contraseñaCifrada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Verificar si el correo ya está registrado
    $queryVerificarCorreo = "SELECT * FROM registro WHERE correo = ?";
    if ($stmtVerificar = mysqli_prepare($enlace, $queryVerificarCorreo)) {
        mysqli_stmt_bind_param($stmtVerificar, "s", $correo);
        mysqli_stmt_execute($stmtVerificar);
        $resultadoVerificar = mysqli_stmt_get_result($stmtVerificar);
        if (mysqli_num_rows($resultadoVerificar) > 0) {
            // Si el correo ya existe, mostrar mensaje y redirigir
            echo '<script>alert("El correo ya está registrado."); location.href="../crearCuenta.html";</script>';
            exit();
        }
        mysqli_stmt_close($stmtVerificar);
    }

    // Preparar la consulta SQL con parámetros
    $insertarDatos = "INSERT INTO registro (nombre, apellido, boleta, correo, contraseña) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($enlace, $insertarDatos)) {
        mysqli_stmt_bind_param($stmt, "ssiss", $usuario, $apellido, $boleta, $correo, $contraseñaCifrada);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            // Redirigir al índice después del registro exitoso
            echo '<script>location.href="../index.html";</script>';
            exit();
        } else {
            // Registrar el error para fines de depuración y mostrar mensaje genérico
            error_log("Error al registrar: " . mysqli_error($enlace));
            echo '<script>alert("Hubo un error en el registro. Por favor, intenta de nuevo."); location.href="../registro.html";</script>';
        }

        // Cerrar la declaración
        mysqli_stmt_close($stmt);
    } else {
        // Error al preparar la consulta
        error_log("Error al preparar la consulta: " . mysqli_error($enlace));
        echo '<script>alert("Hubo un error en el registro. Por favor, intenta de nuevo."); location.href="../registro.html";</script>';
    }

    // Cerrar la conexión
    mysqli_close($enlace);
}
?>
