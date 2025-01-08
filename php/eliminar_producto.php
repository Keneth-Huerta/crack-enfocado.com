
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ventas.php');
    exit();
}

$id_producto = (int)$_GET['id'];

// Verificar que el producto exista y pertenezca al usuario actual
$query = "DELETE FROM productos WHERE idProducto = ? AND usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_producto, $_SESSION['usuario_id']);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['mensaje'] = "Producto eliminado exitosamente.";
    $_SESSION['mensaje_tipo'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el producto: " . mysqli_error($enlace);
    $_SESSION['mensaje_tipo'] = "danger";
}

mysqli_stmt_close($stmt);
mysqli_close($enlace);

header('Location: ventas.php');
exit();
