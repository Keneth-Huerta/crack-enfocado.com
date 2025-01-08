<?php
session_start();
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $productoId = $_GET['id'];

    $stmt = $enlace->prepare("SELECT p.*, u.username, pr.foto_perfil, pr.nombre, pr.apellido, pr.telefono 
                             FROM productos p 
                             JOIN usuarios u ON p.usuario_id = u.id 
                             JOIN perfiles pr ON p.usuario_id = pr.usuario_id 
                             WHERE p.idProducto = ?");
    $stmt->bind_param("i", $productoId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
    } else {
        echo "Producto no encontrado.";
        exit();
    }
} else {
    echo "ID de producto no especificado.";
    exit();
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
    <title><?php echo htmlspecialchars($producto['producto']); ?> - Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem auto;
        }

        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 2rem;
            color: #2ecc71;
            font-weight: bold;
            margin: 1rem 0;
        }

        .description {
            font-size: 1.1rem;
            color: #666;
            margin: 1rem 0;
            line-height: 1.6;
        }

        .seller-info {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .seller-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .seller-details h4 {
            margin: 0;
            color: #333;
        }

        .seller-details p {
            margin: 0;
            color: #666;
        }

        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);
        }

        .btn-whatsapp i {
            font-size: 1.4rem;
        }

        .product-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .product-image {
                height: 300px;
            }

            .product-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php include('header.php'); ?>

    <div class="container">
        <div class="product-container">
            <div class="row">
                <div class="col-md-6">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                            class="product-image" alt="<?php echo htmlspecialchars($producto['producto']); ?>">
                    <?php else: ?>
                        <img src="../media/producto_default.jpg" class="product-image" alt="Imagen no disponible">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h1 class="product-title"><?php echo htmlspecialchars($producto['producto']); ?></h1>
                    <p class="price">$<?php echo number_format($producto['precio'], 2); ?></p>
                    <p class="description"><?php echo htmlspecialchars($producto['descripcion']); ?></p>

                    <div class="seller-info">
                        <img src="<?php echo !empty($producto['foto_perfil']) ? htmlspecialchars($producto['foto_perfil']) : '../media/user.png'; ?>"
                            class="seller-image" alt="Foto de perfil">
                        <div class="seller-details">
                            <h4><?php echo htmlspecialchars($producto['nombre'] . ' ' . $producto['apellido']); ?></h4>
                            <p>Vendedor</p>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != $producto['usuario_id']): ?>
                        <?php if (!empty($producto['telefono'])): ?>
                            <?php
                            $mensaje = "Hola, me interesa tu producto: " . $producto['producto'] . " por $" . $producto['precio'];
                            $mensaje_codificado = urlencode($mensaje);
                            $whatsapp_link = "https://wa.me/{$producto['telefono']}?text={$mensaje_codificado}";
                            ?>
                            <a href="<?php echo $whatsapp_link; ?>" class="btn btn-whatsapp" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                                Contactar por WhatsApp
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="fas fa-phone-slash"></i>
                                Teléfono no disponible
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>