<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.html"); // Redirigir al login si no está autenticado
    exit();
}

// Conexión a la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);

if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el correo del usuario desde la sesión
$correo = $_SESSION['usuario'];

// Consultar los datos del usuario en la base de datos
$query = "SELECT * FROM registro WHERE correo = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

// Si no se encuentran los datos del usuario
if (!$usuario) {
    echo '<script>alert("Error al cargar los datos del usuario.");</script>';
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($enlace);

// Procesar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los nuevos valores
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $boleta = $_POST['boleta'];
    $correo = $_POST['correo'];

    // Subir la imagen (si se selecciona una nueva)
    $imagenPerfil = $usuario['imagen']; // Usar la imagen actual por defecto
    if (isset($_FILES['imagen']['name']) && $_FILES['imagen']['name'] != '') {
        $target_dir = "uploads/"; // Directorio donde guardar las imágenes
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si la imagen es válida
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                $imagenPerfil = $target_file; // Actualizar la imagen
            } else {
                echo '<script>alert("Error al cargar la imagen de perfil.");</script>';
            }
        } else {
            echo '<script>alert("Solo se permiten imágenes JPG, JPEG, PNG o GIF.");</script>';
        }
    }

    // Conectar a la base de datos y actualizar los datos
    $enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
    $updateQuery = "UPDATE registro SET nombre = ?, apellido = ?, boleta = ?, correo = ?, imagen = ? WHERE correo = ?";
    $stmt = mysqli_prepare($enlace, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssssss", $nombre, $apellido, $boleta, $correo, $imagenPerfil, $_SESSION['usuario']);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['usuario'] = $correo; // Actualizar correo en la sesión
        echo '<script>alert("Perfil actualizado con éxito.");</script>';
    } else {
        echo '<script>alert("Error al actualizar los datos.");</script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($enlace);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>

    <div class="form-container">
        <h1>Editar Perfil</h1>
        <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
            </div>

            <div class="form-group">
                <label for="boleta">Boleta:</label>
                <input type="text" name="boleta" id="boleta" value="<?php echo htmlspecialchars($usuario['boleta']); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen de perfil:</label>
                <input type="file" name="imagen" id="imagen" accept="image/*">
                <?php if ($usuario['imagen']): ?>
                    <img src="<?php echo $usuario['imagen']; ?>" alt="Imagen de perfil" width="100" height="100">
                <?php endif; ?>
            </div>

            <button type="submit">Guardar cambios</button>
        </form>
    </div>

</body>
</html>
