<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    error_log("Error en la conexión a la base de datos: " . mysqli_connect_error());
    echo "No se puede conectar a la base de datos en este momento. Inténtalo más tarde.";
    exit;
}

// Iniciar sesión
session_start();

// Función para escapar entradas
function escapar_entrada($entrada, $enlace)
{
    return mysqli_real_escape_string($enlace, trim($entrada));
}

// Validar entrada del formulario
if (isset($_POST['correo'], $_POST['contra'])) {
    $correo = escapar_entrada($_POST['correo'], $enlace);
    $contra = $_POST['contra'];

    // Preparar y ejecutar consulta segura
    $query = "SELECT * FROM registro WHERE correo = ?";
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $correo);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            // Verificar la contraseña (usando password_verify para contraseñas cifradas)
            if (password_verify($contra, $fila['contra'])) {

                // Guardar datos en sesión
                $_SESSION['usuario'] = $fila['correo'];
                header("Location: ../usuario.html");
                exit();
            } else {
                // Contraseña incorrecta
                echo $contra;
                echo $fila['contra'];
                echo '<script>
                    
                    alert("contraseña inválida");
                  
                </script>';
            }
        } else {
            // Usuario no encontrado
            echo '<script>
                alert("Usuario inválido");
                location.href="../index.html";
            </script>';
        }

        mysqli_stmt_close($stmt);
    } else {
        // Error al preparar la consulta
        echo '<script>
            alert("Ocurrió un error en el sistema. Por favor, inténtalo más tarde.");
            location.href="../index.html";
        </script>';
    }
} else {
    // Formulario incompleto
    echo '<script>
        alert("Por favor completa todos los campos");
        location.href="../index.html";
    </script>';
}

// Cerrar conexión
mysqli_close($enlace);
