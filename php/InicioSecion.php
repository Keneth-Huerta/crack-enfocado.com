/**
 * Archivo: /d:/crack-enfocado.com/php/InicioSecion.php
 * 
 * Este archivo maneja la lógica de inicio de sesión para los usuarios.
 * 
 * Configuración de la base de datos:
 * - Servidor: localhost
 * - Usuario de la base de datos: u288355303_Keneth
 * - Contraseña de la base de datos: 1420Genio.
 * - Nombre de la base de datos: u288355303_Usuarios
 * 
 * Funcionalidades:
 * - Conexión a la base de datos utilizando mysqli.
 * - Inicio de sesión de usuario mediante la validación de correo y contraseña.
 * - Uso de sesiones para mantener al usuario autenticado.
 * - Escapado de entradas para prevenir inyecciones SQL.
 * - Uso de consultas preparadas para mayor seguridad.
 * - Verificación de contraseñas cifradas utilizando password_verify.
 * 
 * Flujo del script:
 * 1. Conexión a la base de datos.
 * 2. Inicio de sesión de usuario.
 * 3. Validación de entradas del formulario.
 * 4. Preparación y ejecución de consulta segura.
 * 5. Verificación de la contraseña.
 * 6. Manejo de errores y mensajes al usuario.
 * 7. Cierre de la conexión a la base de datos.
 * 
 * Mensajes de alerta:
 * - Conexión exitosa o fallida a la base de datos.
 * - Contraseña inválida.
 * - Usuario inválido.
 * - Error en el sistema.
 * - Formulario incompleto.
 * 
 * Redirección:
 * - Redirige a Principal.php si el inicio de sesión es exitoso.
 * - Redirige a index.html en caso de errores o formulario incompleto.
 */
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
} else {
    echo "Conexión exitosa";
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
                header("Location: Principal.php");
                exit();
            } else {
                // Contraseña incorrecta
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
