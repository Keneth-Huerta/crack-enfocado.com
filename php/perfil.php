<?php
// Conexión a la base de datos
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conectar a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Verificar si el usuario está logueado y tiene un perfil asignado
if (!isset($_SESSION['perfil_id'])) {
    die("Error: No has iniciado sesión.");
}

$perfil_id = $_SESSION['perfil_id']; // Obtener el ID del perfil desde la sesión

// Procesar formulario de ventas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto = htmlspecialchars($_POST['producto']);
    $precio = floatval($_POST['precio']);
    $descripcion = htmlspecialchars($_POST['descripcion']);

    // Cargar imagen como BLOB
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
    } else {
        $imagen = null;
    }

    // Insertar los datos incluyendo la imagen como BLOB
    $stmt = $enlace->prepare("INSERT INTO productos (producto, precio, descripcion, imagen, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssb", $producto, $precio, $descripcion, $imagen, $perfil_id);

    if ($stmt->execute()) {
        echo "<p>Venta agregada con éxito.</p>";
    } else {
        echo "<p>Error al agregar la venta: " . $stmt->error . "</p>";
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
        }

        .form-container,
        .sales-section {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .sales-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .product-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card img {
            max-width: 100%;
            border-radius: 8px;
        }

        .user-profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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

            <label for="imagen">Selecciona una Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required class="form-control"><br>

            <button type="submit" class="btn btn-primary">Agregar Venta</button>
        </form>
    </div>

    <section class="sales-section">
        <h1 class="sales-title">Materiales</h1>
        <p class="sales-description">Explora la variedad de materiales cargados por los alumnos</p>

        <div class="sales-cards">
            <?php
            // Mostrar productos con las imágenes almacenadas en BLOB
            $sql = "SELECT p.*, pr.foto_perfil FROM productos p 
                    JOIN perfiles pr ON p.usuario_id = pr.id";
            $result = mysqli_query($enlace, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card">';
                    // Mostrar la foto de perfil del usuario
                    echo '<div class="user-profile"><img src="data:image/jpeg;base64,' . base64_encode($row['foto_perfil']) . '" alt="Foto de perfil"></div>';

                    // Mostrar la imagen del producto almacenada como BLOB
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row['imagen']) . '" alt="Imagen del producto">';

                    echo "<h3>" . htmlspecialchars($row['producto']) . "</h3>";
                    echo "<p><strong>Precio:</strong> $" . htmlspecialchars($row['precio']) . "</p>";
                    echo "<p><strong>Descripción:</strong> " . htmlspecialchars($row['descripcion']) . "</p>";
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