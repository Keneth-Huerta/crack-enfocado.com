<?php
// Establecer la conexión con la base de datos
$conn = new mysqli('localhost', 'root', '', 'u288355303_Usuarios');

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
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
</body>
</html>
