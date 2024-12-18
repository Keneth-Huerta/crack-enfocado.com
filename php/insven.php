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

    // Insertar el nuevo producto en la base de datos
    $sql = "INSERT INTO productos (producto, id, precio, descripcion, imagen) 
            VALUES ('$producto', '$id', '$precio', '$descripcion', '$imagen')";
    if (mysqli_query($enlace, $sql)) {
        echo "<p>Producto agregado exitosamente.</p>";
    } else {
        echo "<p>Error al agregar el producto: " . mysqli_error($enlace) . "</p>";
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
            background-color: # #7d1b1b;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: # #7d1b1b;
        }

        /* Estilos para mostrar los productos */
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
</head>
<body>
    <div class="container">
        <h1>Formulario para agregar un nuevo producto</h1>
        <form method="post" >
            <label for="producto" action="ventas.php">Nombre del producto:</label>
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
