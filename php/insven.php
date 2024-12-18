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
    
    // Manejo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $imagen_temp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_tamano = $_FILES['imagen']['size'];
        $imagen_tipo = $_FILES['imagen']['type'];

        // Validaciones para la imagen
        $extensiones_permitidas = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imagen_tipo, $extensiones_permitidas)) {
            echo "<p>Solo se permiten imágenes JPEG, PNG y GIF.</p>";
        } elseif ($imagen_tamano > 5000000) { // Limitar a 5MB
            echo "<p>La imagen es demasiado grande. El tamaño máximo es 5MB.</p>";
        } else {
            // Subir la imagen
            $directorio_imagen = 'uploads/';
            $imagen_ruta = $directorio_imagen . basename($imagen_nombre);
            if (!move_uploaded_file($imagen_temp, $imagen_ruta)) {
                echo "<p>Hubo un error al subir la imagen.</p>";
            }
        }
    } else {
        echo "<p>Por favor, sube una imagen válida.</p>";
    }

    // Validaciones del resto de los campos
    if (empty($producto) || empty($id) || empty($precio) || empty($descripcion) || !isset($imagen_ruta)) {
        echo "<p>Por favor, complete todos los campos.</p>";
    } elseif (!is_numeric($precio) || $precio <= 0) {
        echo "<p>El precio debe ser un número positivo.</p>";
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
            mysqli_stmt_bind_param($stmt_insertar, 'ssdss', $producto, $id, $precio, $descripcion, $imagen_ruta);

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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 1rem;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        textarea {
            resize: vertical;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        p {
            color: red;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulario para agregar un nuevo producto</h1>
        <form method="post" action="ventas.php" enctype="multipart/form-data">
            <label for="producto">Nombre del producto:</label>
            <input type="text" id="producto" name="producto" required><br><br>

            <label for="id">ID del producto:</label>
            <input type="text" id="id" name="id" required><br><br>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br><br>

            <label for="imagen">Seleccionar imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required><br><br>

            <input type="submit" value="Agregar Producto">
        </form>
    </div>
</body>
</html>
