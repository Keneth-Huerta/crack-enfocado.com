<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulario para agregar un nuevo producto</h1>
        <form action="insertar_producto.php" method="post">
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



<?php
session_start();
// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
$sql = "SELECT * FROM productos";
$result = $enlace->query($sql);

echo '<div class="products-container">';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="product-card">';
        echo "<h3>" . $row['producto'] . "</h3>";
        echo "<img src='" . $row['imagen'] . "' alt='" . $row['producto'] . "' class='product-image'><br>";
        echo "<p><strong>Precio:</strong> $" . $row['precio'] . "</p>";
        echo "<p><strong>Descripción:</strong> " . $row['descripcion'] . "</p>";
        echo '</div>';
    }
} else {
    echo "No se encontraron productos.";
}
echo '</div>';

$enlace->close();
?>

<style>
    .products-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        padding: 20px;
        justify-items: center;
    }
    .product-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }
    .product-image {
        max-width: 100%;
        max-height: 200px;
        object-fit: cover;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    .product-card h3 {
        font-size: 18px;
        color: #333;
    }
    .product-card p {
        font-size: 14px;
        color: #666;
    }
</style>
