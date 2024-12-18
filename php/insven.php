<?php
// Conexión a la base de datos
session_start();
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth"; // Usuario de la base de datos
$claveBD = "1420Genio."; // Contraseña de la base de datos
$baseDeDatos = "u288355303_Usuarios"; // Nombre de la base de datos

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Procesar el formulario para agregar un nuevo producto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto = mysqli_real_escape_string($enlace, $_POST['producto']);
    $id = mysqli_real_escape_string($enlace, $_POST['id']);
    $precio = mysqli_real_escape_string($enlace, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($enlace, $_POST['descripcion']);
    $imagen = mysqli_real_escape_string($enlace, $_POST['imagen']);

    // Validaciones
    if (empty($producto) || empty($id) || empty($precio) || empty($descripcion) || empty($imagen)) {
        echo "<p>Por favor, complete todos los campos.</p>";
    } elseif (!is_numeric($precio) || $precio <= 0) {
        echo "<p>El precio debe ser un número positivo.</p>";
    } elseif (!filter_var($imagen, FILTER_VALIDATE_URL)) {
        echo "<p>La URL de la imagen no es válida.</p>";
    } else {
        // Verificar si el producto ya existe en la base de datos (basado en el id o el nombre)
        $sql_verificar = "SELECT * FROM productos WHERE id = ? OR producto = ?";
        $stmt = mysqli_prepare($enlace, $sql_verificar);
        mysqli_stmt_bind_param($stmt, 'ss', $id, $producto);
        mysqli_stmt_execute($stmt);
        $resultado_verificar = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado_verificar) > 0) {
            // El producto ya existe
            echo "<p>El producto ya existe en la base de datos.</p>";
        } else {
            // Insertar el nuevo producto en la base de datos
            $sql_insertar = "INSERT INTO productos (producto, id, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)";
            $stmt_insertar = mysqli_prepare($enlace, $sql_insertar);
            mysqli_stmt_bind_param($stmt_insertar, 'ssdss', $producto, $id, $precio, $descripcion, $imagen);

            if (mysqli_stmt_execute($stmt_insertar)) {
                echo "<p>Producto agregado correctamente.</p>";
            } else {
                echo "<p>Error al agregar el producto: " . mysqli_error($enlace) . "</p>";
            }
        }

        // Cerrar las sentencias preparadas
        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt_insertar);
    }
}

mysqli_close($enlace);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <style>
        /* Estilos como antes */
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulario para agregar un nuevo producto</h1>
        <form method="post" action="ventas.php">
            <label for="producto">Nombre del producto:</label>
            <input type="text" id="producto" name="producto" required><br><br>

            <label for="id">ID del producto:</label>
            <input type="text" id="id" name="id" required><br><br>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br><br>

            <label for="imagen">URL de la imagen:</label>
            <input type="text" id="imagen" name="imagen" required><br><br>

            <input type="submit" value="Agregar Producto">
        </form>
    </div>
</body>
</html>
