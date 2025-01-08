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

// Obtener lista de perfiles
$perfilesQuery = "SELECT id, foto_perfil FROM perfiles";
$perfilesResult = mysqli_query($enlace, $perfilesQuery);

// Procesar formulario de ventas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto = htmlspecialchars($_POST['producto']);
    $precio = floatval($_POST['precio']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $perfil_id = intval($_POST['perfil_id']);

    // Cargar imagen como datos binarios (BLOB)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
    } else {
        $imagen = null;
    }

    // Preparar e insertar en la base de datos
    $stmt = $enlace->prepare("INSERT INTO productos (producto, precio, descripcion, imagen, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssb", $producto, $precio, $descripcion, $imagen, $perfil_id);

    if ($stmt->execute()) {
        echo "<p>Venta agregada con éxito.</p>";
    } else {
        echo "<p>Error al agregar la venta: " . $stmt->error . "</p>";
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
</head>

<body>
    <?php include('header.php'); ?>

    <div class="form-container">
        <h2>Agregar Nueva Venta</h2>
        <form action="ventas.php" method="POST" enctype="multipart/form-data">
            <label for="producto">Producto:</label>
            <input type="text" id="producto" name="producto" required class="form-control"><br>

            <label for="precio">Precio:</label>
            <input type="number" step="0.01" id="precio" name="precio" required class="form-control"><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required class="form-control"></textarea><br>

            <label for="imagen">Selecciona una Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required class="form-control"><br>

            <label for="perfil_id">Selecciona el Perfil:</label>
            <select id="perfil_id" name="perfil_id" required class="form-control">
                <?php while ($perfil = mysqli_fetch_assoc($perfilesResult)) {
                    echo '<option value="' . $perfil['id'] . '">Perfil ID: ' . $perfil['id'] . '</option>';
                } ?>
            </select><br>

            <button type="submit" class="btn btn-primary">Agregar Venta</button>
        </form>
    </div>

    <section class="sales-section">
        <h1 class="sales-title">Materiales</h1>
        <p class="sales-description">Explora la variedad de materiales cargados por los alumnos</p>

        <div class="sales-cards">
            <?php
            $sql = "SELECT p.*, pr.foto_perfil FROM productos p JOIN perfiles pr ON p.usuario_id = pr.id";
            $result = mysqli_query($enlace, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card">';

                    // Mostrar imagen del perfil
                    echo '<div class="user-profile"><img src="data:image/jpeg;base64,' . base64_encode($row['foto_perfil']) . '" alt="Foto de perfil"></div>';

                    // Mostrar imagen del producto desde BLOB
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row['imagen']) . '" alt="Imagen del producto">';

                    echo "<h3>" . htmlspecialchars($row['producto']) . "</h3>";
                    echo "<p><strong>Precio:</strong> $" . htmlspecialchars($row['precio']) . "</p>";
                    echo "<p><strong>Descripción:</strong> " . htmlspecialchars($row['descripcion']) . "</p>";
                    echo '</div>';
                }
            } else {
                echo "<p>No se encontraron productos.</p>";
            }
            mysqli_close($enlace);
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>