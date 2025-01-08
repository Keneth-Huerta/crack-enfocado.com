<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$mensaje = '';
$producto = null;

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ventas.php');
    exit();
}

$id_producto = (int)$_GET['id'];

// Obtener la información actual del producto
$query = "SELECT * FROM productos WHERE idProducto = ? AND usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_producto, $_SESSION['usuario_id']);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($resultado);

// Verificar si el producto existe y pertenece al usuario
if (!$producto) {
    header('Location: ventas.php');
    exit();
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevo_producto = htmlspecialchars($_POST['producto']);
    $nuevo_precio = floatval($_POST['precio']);
    $nueva_descripcion = htmlspecialchars($_POST['descripcion']);

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        // Si se subió una nueva imagen
        $nueva_imagen = file_get_contents($_FILES['imagen']['tmp_name']);
        $query = "UPDATE productos SET producto = ?, precio = ?, descripcion = ?, imagen = ? WHERE idProducto = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "sdsbii", $nuevo_producto, $nuevo_precio, $nueva_descripcion, $nueva_imagen, $id_producto, $_SESSION['usuario_id']);
    } else {
        // Si no se subió una nueva imagen
        $query = "UPDATE productos SET producto = ?, precio = ?, descripcion = ? WHERE idProducto = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "ssdii", $nuevo_producto, $nuevo_precio, $nueva_descripcion, $id_producto, $_SESSION['usuario_id']);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = '<div class="alert alert-success">Producto actualizado exitosamente.</div>';
        // Actualizar la información del producto en la variable
        $producto['producto'] = $nuevo_producto;
        $producto['precio'] = $nuevo_precio;
        $producto['descripcion'] = $nueva_descripcion;
    } else {
        $mensaje = '<div class="alert alert-danger">Error al actualizar el producto: ' . mysqli_error($enlace) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <!-- Meta tags básicos -->
    <meta name="description" content="Red Social Académica del CECyT 3 'Estanislao Ramírez Ruiz'. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="keywords" content="CECyT 3, IPN, red social académica, estudiantes, materiales escolares">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta property="og:title" content="CECyT 3 - Red Social Académica">
    <meta property="og:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta property="og:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">
    <meta property="og:image:alt" content="CECyT 3 Red Social Académica">
    <meta property="og:site_name" content="CECyT 3">
    <meta property="og:locale" content="es_MX">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta name="twitter:title" content="CECyT 3 - Red Social Académica">
    <meta name="twitter:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="twitter:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">

    <!-- WhatsApp -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="CECyT 3">

    <!-- Android -->
    <meta name="theme-color" content="#741931">
    <link rel="manifest" href="/manifest.json">

    <!-- Favicon y íconos -->
    <link rel="mask-icon" href="/media/safari-pinned-tab.svg" color="#741931">
    <link rel="shortcut icon" href="/media/logoweb.svg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .edit-form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .current-image {
            max-width: 300px;
            max-height: 300px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="edit-form-container">
        <h2>Editar Producto</h2>

        <?php echo $mensaje; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="producto" class="form-label">Nombre del Producto:</label>
                <input type="text" class="form-control" id="producto" name="producto"
                    value="<?php echo htmlspecialchars($producto['producto']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label">Precio:</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio"
                    value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <?php if (!empty($producto['imagen'])): ?>
                <div class="mb-3">
                    <label class="form-label">Imagen Actual:</label><br>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                        alt="Imagen actual del producto" class="current-image">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="imagen" class="form-label">Nueva Imagen (opcional):</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            </div>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="ventas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_close($enlace);
?>