<?php
// Configurar límites de subida de archivos
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', 300);
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
    $usuario_id = $_SESSION['usuario_id'];

    try {
        // Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            // Verificar el tipo de archivo
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['imagen']['type'];

            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Tipo de archivo no permitido. Por favor, sube una imagen JPG, PNG o GIF.");
            }

            // Leer la imagen
            $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));

            // Query con imagen
            $query = "INSERT INTO productos (producto, precio, descripcion, imagen, usuario_id) 
                     VALUES ('$producto', $precio, '$descripcion', '$imagen', $usuario_id)";
        } else {
            // Query sin imagen
            $query = "INSERT INTO productos (producto, precio, descripcion, usuario_id) 
                     VALUES ('$producto', $precio, '$descripcion', $usuario_id)";
        }

        // Ejecutar la consulta
        if (mysqli_query($enlace, $query)) {
            echo "<div class='alert alert-success'>Venta agregada con éxito.</div>";
        } else {
            throw new Exception("Error en la inserción: " . mysqli_error($enlace));
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
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

        .btn-success {
            background-color: #25D366;
            color: white;
            border: none;
        }

        .btn-success:hover {
            background-color: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .acciones-producto .btn-success {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .acciones-producto .btn-success i {
            font-size: 1.2em;
        }

        .rating-section {
            margin: 15px 0;
        }

        .stars-display {
            display: inline-block;
            margin-right: 10px;
        }

        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }

        .rating-input input {
            display: none;
        }

        .rating-input label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            margin: 0 2px;
        }

        .rating-input label:hover,
        .rating-input label:hover~label,
        .rating-input input:checked~label {
            color: #ffb400;
        }

        .rating-input label:hover:before,
        .rating-input label:hover~label:before,
        .rating-input input:checked~label:before {
            content: "★";
            position: absolute;
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
            $sql = "SELECT p.*, u.username, pr.foto_perfil, pr.nombre, pr.apellido, pr.telefono 
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
                            <a href="perfil.php?usuario_id=<?php echo $row['usuario_id']; ?>">
                                <img src="<?php echo !empty($row['foto_perfil']) ? htmlspecialchars($row['foto_perfil']) : '../media/user.png'; ?>"
                                    alt="Foto de perfil">
                            </a>
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

                            <!-- Sistema de calificación -->
                            <div class="rating-section">
                                <?php
                                // Obtener calificación promedio
                                $query_rating = "SELECT AVG(estrellas) as promedio, COUNT(*) as total 
                        FROM calificaciones 
                        WHERE producto_id = " . $row['idProducto'];
                                $result_rating = mysqli_query($enlace, $query_rating);
                                $rating_data = mysqli_fetch_assoc($result_rating);
                                $promedio = round($rating_data['promedio'], 1);
                                ?>
                                <div class="stars-display">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $promedio ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <span>(<?php echo $rating_data['total']; ?> calificaciones)</span>
                                </div>

                                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != $row['usuario_id']): ?>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#ratingModal<?php echo $row['idProducto']; ?>">
                                        Calificar
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#reportModal<?php echo $row['idProducto']; ?>">
                                        Reportar
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Resto del código existente... -->
                        </div>

                        <!-- Modal de Calificación -->
                        <div class="modal fade" id="ratingModal<?php echo $row['idProducto']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Calificar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="calificar_producto.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="producto_id" value="<?php echo $row['idProducto']; ?>">
                                            <div class="rating-input">
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <input type="radio" name="estrellas" value="<?php echo $i; ?>" id="star<?php echo $i; ?>_<?php echo $row['idProducto']; ?>">
                                                    <label for="star<?php echo $i; ?>_<?php echo $row['idProducto']; ?>">☆</label>
                                                <?php endfor; ?>
                                            </div>
                                            <textarea name="comentario" class="form-control mt-3" placeholder="Escribe tu comentario (opcional)"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Enviar Calificación</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Reporte -->
                        <div class="modal fade" id="reportModal<?php echo $row['idProducto']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reportar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="reportar_producto.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="producto_id" value="<?php echo $row['idProducto']; ?>">
                                            <select name="motivo" class="form-select mb-3" required>
                                                <option value="">Selecciona un motivo</option>
                                                <option value="contenido_inapropiado">Contenido inapropiado</option>
                                                <option value="spam">Spam</option>
                                                <option value="producto_prohibido">Producto prohibido</option>
                                                <option value="informacion_falsa">Información falsa</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                            <textarea name="descripcion" class="form-control" placeholder="Describe el problema" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">Enviar Reporte</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>