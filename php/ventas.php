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

        .producto-imagen {
            width: 100%;
            max-height: 300px;
            overflow: hidden;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .producto-imagen img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .lista-productos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .producto-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .producto-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .producto-detalles {
            padding: 15px;
        }

        .producto-detalles h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }

        .precio {
            font-size: 1.25em;
            color: #2ecc71;
            font-weight: bold;
            margin: 10px 0;
        }

        .descripcion {
            color: #666;
            margin-bottom: 15px;
        }

        .acciones-producto {
            display: flex;
            gap: 10px;
            margin-top: 15px;
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

        .producto-imagen {
            width: 100%;
            height: 300px;
            overflow: hidden;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
        }

        .producto-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .producto-detalles {
            padding: 15px;
            background-color: white;
            border-radius: 8px;
        }

        .producto-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje_tipo']; ?> alert-dismissible fade show" role="alert">
            <?php
            echo $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);
            unset($_SESSION['mensaje_tipo']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
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
            $sql = "SELECT p.*, u.username, pr.foto_perfil, pr.nombre, pr.apellido 
            FROM productos p 
            JOIN perfiles pr ON p.usuario_id = pr.usuario_id 
            JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.idProducto DESC";
            $result = mysqli_query($enlace, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <div class="producto-item">
                        <!-- Información del usuario -->
                        <div class="user-profile">
                            <img src="<?php echo !empty($row['foto_perfil']) ? htmlspecialchars($row['foto_perfil']) : '../media/user.png'; ?>"
                                alt="Foto de perfil">
                            <p><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></p>
                        </div>

                        <!-- Imagen del producto -->
                        <div class="producto-imagen">
                            <?php if (!empty($row['imagen'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagen']); ?>"
                                    alt="Imagen del producto">
                            <?php else: ?>
                                <img src="../media/producto_default.jpg" alt="Imagen no disponible">
                            <?php endif; ?>
                        </div>

                        <!-- Detalles del producto -->
                        <div class="producto-detalles">
                            <h3><?php echo htmlspecialchars($row['producto']); ?></h3>
                            <p class="precio">$<?php echo number_format($row['precio'], 2); ?></p>
                            <p class="descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>

                            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $row['usuario_id']): ?>
                                <div class="acciones-producto">
                                    <a href="editar_producto.php?id=<?php echo $row['idProducto']; ?>"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="eliminar_producto.php?id=<?php echo $row['idProducto']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="sin-productos">
                <p><i class="fas fa-store"></i> No hay productos disponibles.</p>
              </div>';
            }
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>