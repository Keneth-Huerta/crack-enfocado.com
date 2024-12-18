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
            color: #ff5722;
            margin-bottom: 20px;
        }

        .buy-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff5722;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .buy-button:hover {
            background-color: #e64a19;
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
    <h1>Interfaz de Ventas</h1>
    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="producto">Nombre del Producto</label>
                <input type="text" id="producto" name="producto" required>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad</label>
                <input type="number" id="cantidad" name="cantidad" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
            </div>
            <div class="form-group">
                <button type="submit" name="submit">Registrar Venta</button>
            </div>
        </form>
    </div>

<?php
// Establecer la conexión con la base de datos
$conn = new mysqli('localhost', 'root', '', 'ventas_db');

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>


<?php
if (isset($_POST['submit'])) {
    $producto = $_POST['producto'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $sql = "INSERT INTO ventas (producto, cantidad, precio) VALUES ('$producto', '$cantidad', '$precio')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Venta registrada exitosamente.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

$sql = "SELECT * FROM ventas";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $total = $row['cantidad'] * $row['precio'];
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['producto'] . "</td><td>" . $row['cantidad'] . "</td><td>" . $row['precio'] . "</td><td>" . $total . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay ventas registradas.</p>";
}

$conn->close();
?>
*/

</body>
</html>

