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
    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
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
        /* ... Mantener los estilos anteriores ... */
        .product-image {
            max-width: 300px;
            max-height: 300px;
            object-fit: contain;
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