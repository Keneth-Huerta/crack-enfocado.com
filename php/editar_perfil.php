<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Iniciar sesión
session_start();

// Obtener el ID de usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];  // Suponiendo que el ID de usuario está guardado en la sesión

// Comprobar si el usuario está logueado
if (!isset($usuario_id)) {
    header("Location: ../login.html");  // Redirigir si no está logueado
    exit();
}

// Obtener los datos del perfil
$query = "SELECT * FROM perfiles WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);

// Si no existe el perfil, redirigir para crearlo
if (!$perfil) {
    header("Location: crear_perfil.php");  // Redirigir a la página para crear el perfil
    exit();
}

// Variables para almacenar los valores de los campos
$nombre = $perfil['nombre'] ?? '';
$apellido = $perfil['apellido'] ?? '';
$carrera = $perfil['carrera'] ?? '';
$foto_perfil = $perfil['foto_perfil'] ?? '';
$foto_portada = $perfil['foto_portada'] ?? '';
$informacion_extra = $perfil['informacion_extra'] ?? '';

// Actualizar perfil si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellido = htmlspecialchars($_POST['apellido']);
    $carrera = $_POST['carrera'];
    $informacion_extra = htmlspecialchars($_POST['informacion_extra']);
    $foto_perfil = $_FILES['foto_perfil']['name'];
    $foto_portada = $_FILES['foto_portada']['name'];

    // Subir las imágenes si fueron seleccionadas
    if (!empty($foto_perfil)) {
        $target_dir = "../uploads/perfiles/";
        $target_file = $target_dir . basename($_FILES["foto_perfil"]["name"]);
        move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file);
    } else {
        $foto_perfil = $perfil['foto_perfil'];  // Mantener la foto si no se seleccionó una nueva
    }

    if (!empty($foto_portada)) {
        $target_file_portada = $target_dir . basename($_FILES["foto_portada"]["name"]);
        move_uploaded_file($_FILES["foto_portada"]["tmp_name"], $target_file_portada);
    } else {
        $foto_portada = $perfil['foto_portada'];  // Mantener la foto de portada si no se seleccionó una nueva
    }

    // Actualizar los datos en la base de datos
    $update_query = "UPDATE perfiles SET nombre = ?, apellido = ?, carrera = ?, foto_perfil = ?, foto_portada = ?, informacion_extra = ? WHERE usuario_id = ?";
    $stmt_update = mysqli_prepare($enlace, $update_query);
    mysqli_stmt_bind_param($stmt_update, "ssssssi", $nombre, $apellido, $carrera, $foto_perfil, $foto_portada, $informacion_extra, $usuario_id);

    if (mysqli_stmt_execute($stmt_update)) {
        echo '<script>alert("Perfil actualizado exitosamente."); window.location.href = "ver_perfil.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar el perfil. Inténtalo nuevamente.");</script>';
    }

    mysqli_stmt_close($stmt_update);
}

// Cerrar la conexión
mysqli_stmt_close($stmt);
mysqli_close($enlace);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../css/misestilos.css">
</head>
<body>

<div class="form-container">
    <h1>Editar Perfil</h1>
    <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required placeholder="Nombre Completo">
        </div>
        <div class="form-group">
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required placeholder="Apellido Completo">
        </div>
        <div class="form-group">
            <select name="carrera" required>
                <option value="Técnico en Aeronáutica" <?php if ($carrera == "Técnico en Aeronáutica") echo "selected"; ?>>Técnico en Aeronáutica</option>
                <option value="Técnico en Computación" <?php if ($carrera == "Técnico en Computación") echo "selected"; ?>>Técnico en Computación</option>
                <option value="Técnico en Manufactura Asistida por Computadora" <?php if ($carrera == "Técnico en Manufactura Asistida por Computadora") echo "selected"; ?>>Técnico en Manufactura Asistida por Computadora</option>
                <option value="Técnico en Sistemas Automotrices" <?php if ($carrera == "Técnico en Sistemas Automotrices") echo "selected"; ?>>Técnico en Sistemas Automotrices</option>
                <option value="Técnico en Sistemas Digitales" <?php if ($carrera == "Técnico en Sistemas Digitales") echo "selected"; ?>>Técnico en Sistemas Digitales</option>
            </select>
        </div>
        <div class="form-group">
            <input type="file" name="foto_perfil" accept="image/*" placeholder="Foto de Perfil">
        </div>
        <div class="form-group">
            <input type="file" name="foto_portada" accept="image/*" placeholder="Foto de Portada">
        </div>
        <div class="form-group">
            <textarea name="informacion_extra" placeholder="Información Extra"><?php echo htmlspecialchars($informacion_extra); ?></textarea>
        </div>
        <button type="submit">Actualizar Perfil</button>
    </form>
</div>

</body>
</html>
