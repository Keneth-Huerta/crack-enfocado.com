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


        @media (max-width: 768px) {
            .sales-cards {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>





    <section class="sales-section">
        <h1 class="sales-title">Materiales</h1>
        <p class="sales-description">Explora la variedad de materiales cargados por los alumnos</p>

        <div class="sales-cards">
            <?php
                $productos = [
                    ["title" => "Producto 1", "description" => "Descripción", "price" => "$49.99"],
                    ["title" => "Producto 2", "description" => "Descripción", "price" => "$79.99"],
                    ["title" => "Producto 3", "description" => "Descripción", "price" => "$99.99"],
                ];

                foreach ($productos as $producto) {
                    echo "<div class='sales-card'>";
                    echo "<h2 class='product-title'>{$producto['title']}</h2>";
                    echo "<p class='product-description'>{$producto['description']}</p>";
                    echo "<p class='product-price'>{$producto['price']}</p>";
                    echo "<a href='#' class='buy-button'>Comprar ahora</a>";
                    echo "<p><a href='https://crack-enfocado.com/php/descprod.php' class='des-button'>Descripcion</a></p>";
                    echo "</div>";
                }
            ?>
        </div>
    </section>
</body>
</html>