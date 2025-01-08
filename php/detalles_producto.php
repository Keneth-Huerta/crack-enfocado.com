<?php
session_start();
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $productoId = $_GET['id'];

    $stmt = $enlace->prepare("SELECT p.*, u.username, pr.foto_perfil 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Agrega tus estilos CSS aquí */
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h2>Detalles del producto</h2>
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($producto['imagen'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>" class="img-fluid" alt="Producto">
                <?php else: ?>
                    <img src="../media/producto_default.jpg" class="img-fluid" alt="Imagen no disponible">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h3><?php echo htmlspecialchars($producto['producto']); ?></h3>
                <p class="price">$<?php echo number_format($producto['precio'], 2); ?></p>
                <p class="description"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <small class="text-muted">Vendedor: <?php echo htmlspecialchars($producto['username']); ?></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>