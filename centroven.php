<?php
// Iniciar sesión para manejar el carrito
session_start();

// Array de productos (puede venir de una base de datos)
$productos = [
    1 => ["nombre" => "Producto A", "precio" => 100],
    2 => ["nombre" => "Producto B", "precio" => 200],
    3 => ["nombre" => "Producto C", "precio" => 300],
];

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if (isset($_GET['agregar'])) {
    $idProducto = $_GET['agregar'];
    if (isset($productos[$idProducto])) {
        $_SESSION['carrito'][] = $productos[$idProducto];
        echo "<p style='color: green;'>Producto agregado al carrito.</p>";
    } else {
        echo "<p style='color: red;'>Producto no válido.</p>";
    }
}

// Limpiar el carrito
if (isset($_GET['limpiar'])) {
    $_SESSION['carrito'] = [];
    echo "<p style='color: orange;'>Carrito limpiado.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .producto, .carrito { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
        .producto h3, .carrito h3 { margin: 0; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>
    <h1>Centro de Ventas</h1>

    <!-- Listado de productos -->
    <div>
        <h2>Productos Disponibles</h2>
        <?php foreach ($productos as $id => $producto): ?>
            <div class="producto">
                <h3><?= $producto['nombre'] ?></h3>
                <p>Precio: $<?= $producto['precio'] ?></p>
                <a href="?agregar=<?= $id ?>">Agregar al carrito</a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Carrito de compras -->
    <div>
        <h2>Carrito de Compras</h2>
        <?php if (!empty($_SESSION['carrito'])): ?>
            <div class="carrito">
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <p><?= $item['nombre'] ?> - $<?= $item['precio'] ?></p>
                <?php endforeach; ?>
                <hr>
                <p><strong>Total: $<?= array_sum(array_column($_SESSION['carrito'], 'precio')) ?></strong></p>
                <a href="?limpiar=1">Limpiar Carrito</a>
            </div>
        <?php else: ?>
            <p>El carrito está vacío.</p>
        <?php endif; ?>
    </div>
</body>
</html>
