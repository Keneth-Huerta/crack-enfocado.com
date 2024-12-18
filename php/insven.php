<?php
// Este es el código PHP que procesa los datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos
    $enlace = mysqli_connect("localhost", "u288355303_Keneth", "1420Genio.", "u288355303_Usuarios");
    if (!$enlace) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Recibir los datos del formulario
    $producto = mysqli_real_escape_string($enlace, $_POST['producto']);
    $precio = mysqli_real_escape_string($enlace, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($enlace, $_POST['descripcion']);
    $imagen = $_FILES['imagen']; // Archivo de imagen

    // Verificar si el producto ya existe en la base de datos
    $sqlCheck = "SELECT * FROM productos WHERE producto = '$producto'";
    $resultCheck = mysqli_query($enlace, $sqlCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        echo "El producto ya existe en la base de datos.";
    } else {
        // Verificar si se ha subido un archivo correctamente
        if ($imagen['error'] == 0) {
            // Verificar tipo y tamaño del archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5 MB

            if (in_array($imagen['type'], $allowedTypes) && $imagen['size'] <= $maxSize) {
                // Directorio de destino para la imagen
                $directorioDestino = "uploads/";

                // Crear el directorio si no existe
                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0777, true);
                }

                // Obtener nombre de archivo y crear una ruta
                $nombreArchivo = basename($imagen['name']);
                $rutaArchivo = $directorioDestino . $nombreArchivo;

                // Mover el archivo al directorio de destino
                if (move_uploaded_file($imagen['tmp_name'], $rutaArchivo)) {
                    // Insertar los datos del producto en la base de datos
                    $sqlInsert = "INSERT INTO productos (producto, precio, descripcion, imagen) 
                                  VALUES ('$producto', '$precio', '$descripcion', '$rutaArchivo')";
                    if (mysqli_query($enlace, $sqlInsert)) {
                        echo "Producto agregado exitosamente.";
                    } else {
                        echo "Error al agregar el producto: " . mysqli_error($enlace);
                    }
                } else {
                    echo "Error al subir la imagen.";
                }
            } else {
                echo "El archivo no es válido o excede el tamaño permitido (5 MB).";
            }
        } else {
            echo "Por favor, selecciona una imagen.";
        }
    }

    // Cerrar la conexión
    mysqli_close($enlace);
}
?>

<!-- Aquí va el formulario HTML -->
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
        textarea,
        input[type="file"] {
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
        <form method="post" action="ventas.php" enctype="multipart/form-data">
            <label for="producto">Nombre del producto:</label>
            <input type="text" id="producto" name="producto" required><br><br>

            <label for="id">ID del producto:</label>
            <input type="text" id="id" name="id" required><br><br>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br><br>

            <label for="imagen">Selecciona la imagen del producto:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required><br><br>

            <input type="submit" value="Agregar Producto">
        </form>
    </div>
</body>
</html>
