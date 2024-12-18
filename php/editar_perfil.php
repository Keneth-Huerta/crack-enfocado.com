<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$enlace = mysqli_connect("localhost", "u288355303_Keneth", "1420Genio.", "u288355303_Usuarios");
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$usuario = $_SESSION['usuario']; // Correo del usuario (guardado en la sesión)
$query = "SELECT u.id, u.username, u.correo, p.nombre, p.apellido, p.carrera, p.semestre, p.foto_perfil, p.foto_portada, p.informacion_extra 
          FROM usuarios u 
          LEFT JOIN perfiles p ON u.id = p.usuario_id 
          WHERE u.correo = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);

// Si el usuario no tiene perfil, redirigirlo
if (!$perfil) {
    echo "No se encontró el perfil.";
    exit();
}

// Procesar actualización del perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $carrera = $_POST['carrera'];
    $semestre = $_POST['semestre'];
    $informacion_extra = $_POST['informacion_extra'];

    // Subir nueva foto de perfil si es necesario
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $foto_perfil = "path/to/your/uploads/" . $_FILES['foto_perfil']['name'];
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_perfil);
    } else {
        $foto_perfil = $perfil['foto_perfil']; // Mantener la foto anterior si no se sube una nueva
    }

    // Subir nueva foto de portada si es necesario
    if (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] == 0) {
        $foto_portada = "path/to/your/uploads/" . $_FILES['foto_portada']['name'];
        move_uploaded_file($_FILES['foto_portada']['tmp_name'], $foto_portada);
    } else {
        $foto_portada = $perfil['foto_portada']; // Mantener la foto anterior si no se sube una nueva
    }

    // Actualizar datos en la base de datos
    $update_query = "UPDATE perfiles 
                     SET nombre = ?, apellido = ?, carrera = ?, semestre = ?, foto_perfil = ?, foto_portada = ?, informacion_extra = ? 
                     WHERE usuario_id = ?";
    $stmt = mysqli_prepare($enlace, $update_query);
    mysqli_stmt_bind_param($stmt, "sssssssi", $nombre, $apellido, $carrera, $semestre, $foto_perfil, $foto_portada, $informacion_extra, $perfil['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Perfil actualizado con éxito.";
    } else {
        echo "Error al actualizar el perfil.";
    }
}

mysqli_stmt_close($stmt);
mysqli_close($enlace);
?>

<!-- Formulario de edición de perfil -->
<div class="form-container">
    <h1>Editar Perfil</h1>
    <form action="editar_perfil.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($perfil['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($perfil['apellido']); ?>" required>
        </div>

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <input type="text" name="carrera" value="<?php echo htmlspecialchars($perfil['carrera']); ?>" required>
        </div>

        <div class="form-group">
            <label for="semestre">Semestre:</label>
            <input type="number" name="semestre" value="<?php echo htmlspecialchars($perfil['semestre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="informacion_extra">Información adicional:</label>
            <textarea name="informacion_extra"><?php echo htmlspecialchars($perfil['informacion_extra']); ?></textarea>
        </div>

        <div class="form-group upload-section">
            <label for="foto_perfil">Foto de perfil:</label>
            <input type="file" name="foto_perfil">
        </div>

        <div class="form-group upload-section">
            <label for="foto_portada">Foto de portada:</label>
            <input type="file" name="foto_portada">
        </div>

        <button type="submit">Actualizar perfil</button>
    </form>
</div>
