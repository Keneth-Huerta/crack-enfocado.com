
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
