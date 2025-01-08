<?php
// Conexión a la base de datos
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Procesar formulario de ventas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto = htmlspecialchars($_POST['producto']);
    $precio = floatval($_POST['precio']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $usuario_id = $_SESSION['usuario_id']; // Usar el ID del usuario en sesión

    // Manejo de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);

        $stmt = $enlace->prepare("INSERT INTO productos (producto, precio, descripcion, imagen, usuario_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsbi", $producto, $precio, $descripcion, $imagen, $usuario_id);
    } else {
        $stmt = $enlace->prepare("INSERT INTO productos (producto, precio, descripcion, usuario_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $producto, $precio, $descripcion, $usuario_id);
    }

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Venta agregada con éxito.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al agregar la venta: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .sales-section {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .sales-title {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }

        .sales-description {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .sales-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .user-profile p {
            margin: 0;
            color: #333;
            font-weight: bold;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .product-card h3 {
            color: #333;
            margin: 10px 0;
            font-size: 1.2em;
        }

        .product-card p {
            color: #666;
            margin: 5px 0;
        }

        .product-card p strong {
            color: #333;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .alert {
            padding: 15px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .sales-cards {
                grid-template-columns: 1fr;
            }

            .form-container,
            .sales-section {
                margin: 10px;
                padding: 10px;
            }

            .product-image {
                height: 150px;
            }
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="form-container">
        <h2>Agregar Nueva Venta</h2>
        <form action="ventas.php" method="POST" enctype="multipart/form-data">
            <label for="producto">Producto:</label>
            <input type="text" id="producto" name="producto" required class="form-control"><br>

            <label for="precio">Precio:</label>
            <input type="number" step="0.01" id="precio" name="precio" required class="form-control"><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required class="form-control"></textarea><br>

            <label for="imagen">Imagen del Producto:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" class="form-control"><br>

            <button type="submit" class="btn btn-primary">Agregar Venta</button>
        </form>
    </div>

    <section class="sales-section">
        <h1 class="sales-title">Materiales</h1>
        <p class="sales-description">Explora la variedad de materiales cargados por los alumnos</p>

        <div class="sales-cards">
            <?php
            $sql = "SELECT p.*, pr.foto_perfil, pr.nombre, pr.apellido 
                    FROM productos p 
                    JOIN perfiles pr ON p.usuario_id = pr.usuario_id";
            $result = mysqli_query($enlace, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card">';
                    // Mostrar información del usuario
                    echo '<div class="user-profile">';
                    if (!empty($row['foto_perfil'])) {
                        echo '<img src="' . htmlspecialchars($row['foto_perfil']) . '" alt="Foto de perfil">';
                    }
                    echo '<p>' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</p>';
                    echo '</div>';

                    // Mostrar imagen del producto
                    if (!empty($row['imagen'])) {
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['imagen']) . '" 
                              alt="Imagen del producto" class="product-image">';
                    }

                    echo "<h3>" . htmlspecialchars($row['producto']) . "</h3>";
                    echo "<p><strong>Precio:</strong> $" . number_format($row['precio'], 2) . "</p>";
                    echo "<p><strong>Descripción:</strong> " . htmlspecialchars($row['descripcion']) . "</p>";

                    // Agregar botones de edición si el usuario es el propietario
                    if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $row['usuario_id']) {
                        echo '<div class="actions">';
                        echo '<a href="editar_producto.php?id=' . $row['idProducto'] . '" class="btn btn-primary btn-sm">Editar</a> ';
                        echo '<a href="eliminar_producto.php?id=' . $row['idProducto'] . '" 
                              class="btn btn-danger btn-sm" 
                              onclick="return confirm(\'¿Estás seguro de que deseas eliminar este producto?\')">Eliminar</a>';
                        echo '</div>';
                    }

                    echo '</div>';
                }
            } else {
                echo "<p>No se encontraron productos.</p>";
            }
            mysqli_close($enlace);
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>