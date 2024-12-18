<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
</head>
<body>
    <h1>Formulario para agregar un nuevo producto</h1>
    <form action="insertar_producto.php" method="post">
        <label for="nombre">Nombre del producto:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" required><br><br>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea><br><br>

        <label for="categoria">Categoría:</label>
        <input type="text" id="categoria" name="categoria" required><br><br>

        <input type="submit" value="Agregar Producto">
    </form>
</body>
</html>

<?php
// Conectar a la base de datos
$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$base_datos = 'u288355303_Usuarios'; // Cambia esto por el nombre de tu base de datos

$conn = new mysqli($host, $usuario, $contraseña, $base_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto = $_POST['producto'];
    $id = $_POST['id'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_POST['imagen'];

    // Preparar la consulta SQL
    $sql = "INSERT INTO productos VALUES ('$producto', '$id', '$precio', '$descripcion','$imagen')";

    if ($conn->query($sql) === TRUE) {
        echo "Producto agregado correctamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

