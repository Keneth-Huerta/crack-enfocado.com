<?php
// Conexión a la base de datos
session_start();
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth"; // Usuario de la base de datos
$claveBD = "1420Genio."; // Contraseña de la base de datos
$baseDeDatos = "u288355303_Usuarios"; // Nombre de la base de datos

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .sales-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .sales-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }

        .sales-description {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }

        .sales-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .sales-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            text-align: left;
            transition: transform 0.3s;
        }

        .sales-card:hover {
            transform: translateY(-10px);
        }

        .product-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 1rem;
            color: #666;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #7d1b1b;
            margin-bottom: 20px;
        }

        .buy-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #7d1b1b;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .buy-button:hover {
            background-color: #7d1b1b;
        }

        .des-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #7d1b1b;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .des-button:hover {
            background-color: #7d1b1b;
        }

        /* Estilos para mostrar los productos */
        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
            justify-items: center;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .product-card h3 {
            font-size: 18px;
            color: #333;
        }

        .product-card p {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <section class="sales-section">
        <h1 class="sales-title">Materiales</h1>
        <p class="sales-description">Explora la variedad de materiales cargados por los alumnos</p>

        <div class="sales-cards">
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Consultar los productos de la base de datos
            $sql = "SELECT * FROM productos";
            $result = mysqli_query($enlace, $sql);

            if (mysqli_num_rows($result) > 0) {
                // Mostrar los productos
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card">';
                    echo "<h3>" . htmlspecialchars($row['producto']) . "</h3>";
                    echo "<img src='" . htmlspecialchars($row['imagen']) . "' alt='" . htmlspecialchars($row['producto']) . "' class='product-image'><br>";
                    echo "<p><strong>Precio:</strong> $" . htmlspecialchars($row['precio']) . "</p>";
                    echo "<p><strong>Descripción:</strong> " . htmlspecialchars($row['descripcion']) . "</p>";
                    echo '<a href="#" class="buy-button">Comprar</a>'; // Botón de compra (puedes redirigir a una página de compra)
                    echo '</div>';
                }
            } else {
                echo "<p>No se encontraron productos.</p>";
            }

            // Cerrar la conexión
            mysqli_close($enlace);
            ?>
        </div>
    </section>

</body>

</html>