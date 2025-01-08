/**
 * Archivo de conexión a la base de datos.
 * 
 * Este archivo establece una conexión a la base de datos utilizando las credenciales proporcionadas.
 * 
 * Variables:
 * - $servidor: Dirección del servidor de la base de datos.
 * - $usuarioBD: Nombre de usuario para acceder a la base de datos.
 * - $claveBD: Contraseña para acceder a la base de datos.
 * - $baseDeDatos: Nombre de la base de datos a la que se desea conectar.
 * 
 * Funciones:
 * - mysqli_connect: Crea una conexión a la base de datos.
 * - mysqli_connect_error: Devuelve una descripción del último error de conexión.
 * 
 * Si la conexión falla, se detiene la ejecución del script y se muestra un mensaje de error.
 */
<?php
// Datos de la base de datos
$servidor = "localhost"; // El servidor de la base de datos
$usuarioBD = "u288355303_Keneth"; // Usuario de la base de datos
$claveBD = "1420Genio."; // Contraseña de la base de datos
$baseDeDatos = "u288355303_Usuarios"; // Nombre de la base de datos

// Crear conexión
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);

// Verificar conexión
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
