<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");  // Si no está logueado, redirigimos al login
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

// Obtener los datos del usuario para pre-poblar el formulario
$usuario_id = $_SESSION['usuario_id'];

$query = "SELECT u.username, u.correo, p.nombre, p.apellido, p.carrera, p.semestre, p.foto_perfil, p.informacion_extra 
          FROM usuarios u
          LEFT JOIN perfiles p ON u.id = p.usuario_id
          WHERE u.id = ?";

if ($stmt = mysqli_prepare($enlace, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Si el usuario tiene un perfil, lo obtenemos
        $nombre = $fila['nombre'];
        $apellido = $fila['apellido'];
        $correo = $fila['correo'];
        $carrera = $fila['carrera'];
        $semestre = $fila['semestre'];
        $foto_perfil = $fila['foto_perfil'];
        $informacion_extra = $fila['informacion_extra'];
    }
    mysqli_stmt_close($stmt);
} else {
    // Error en la consulta
    echo "Error al obtener los datos del usuario.";
}

// Cerrar conexión
mysqli_close($enlace);

// Procesar el formulario de edición
if (isset($_POST['guardar_perfil'])) {
    $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($enlace, $_POST['apellido']);
    $correo = mysqli_real_escape_string($enlace, $_POST['correo']);
    $carrera = mysqli_real_escape_string($enlace, $_POST['carrera']);
    $semestre = mysqli_real_escape_string($enlace, $_POST['semestre']);
    $informacion_extra = mysqli_real_escape_string($enlace, $_POST['informacion_extra']);

    // Si se sube una nueva foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $foto_perfil = $_FILES['foto_perfil']['name'];
        $ruta_foto = 'uploads/' . basename($foto_perfil);
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_foto);
    } else {
        // Si no hay nueva foto, se mantiene la actual
        $foto_perfil = $_POST['foto_perfil_actual'];
    }

    // Actualizar los datos en la base de datos
    $query = "UPDATE perfiles 
              SET nombre = ?, apellido = ?, correo = ?, carrera = ?, semestre = ?, foto_perfil = ?, informacion_extra = ? 
              WHERE usuario_id = ?";

    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "ssssisss", $nombre, $apellido, $correo, $carrera, $semestre, $foto_perfil, $informacion_extra, $usuario_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($enlace);

    // Redirigir al perfil actualizado
    header("Location: perfil.php");
    exit();
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
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera:</label>
                <select name="carrera" id="carrera" required>
                    <option value="Técnico en Aeronáutica" <?php echo ($carrera == 'Técnico en Aeronáutica') ? 'selected' : ''; ?>>Técnico en Aeronáutica</option>
                    <option value="Técnico en Computación" <?php echo ($carrera == 'Técnico en Computación') ? 'selected' : ''; ?>>Técnico en Computación</option>
                    <option value="Técnico en Manufactura Asistida por Computadora" <?php echo ($carrera == 'Técnico en Manufactura Asistida por Computadora') ? 'selected' : ''; ?>>Técnico en Manufactura Asistida por Computadora</option>
                    <option value="Técnico en Sistemas Automotrices" <?php echo ($carrera == 'Técnico en Sistemas Automotrices') ? 'selected' : ''; ?>>Técnico en Sistemas Automotrices</option>
                    <option value="Técnico en Sistemas Digitales" <?php echo ($carrera == 'Técnico en Sistemas Digitales') ? 'selected' : ''; ?>>Técnico en Sistemas Digitales</option>
                </select>
            </div>

            <div class="form-group">
                <label for="semestre">Semestre:</label>
                <input type="number" id="semestre" name="semestre" value="<?php echo htmlspecialchars($semestre); ?>" required>
            </div>

            <div class="form-group">
                <label for="informacion_extra">Información adicional:</label>
                <textarea name="informacion_extra" id="informacion_extra"><?php echo htmlspecialchars($informacion_extra); ?></textarea>
            </div>

            <div class="form-group">
                <label for="foto_perfil">Foto de perfil:</label>
                <input type="file" name="foto_perfil" id="foto_perfil">
                <input type="hidden" name="foto_perfil_actual" value="<?php echo $foto_perfil; ?>">
                <img src="uploads/<?php echo $foto_perfil; ?>" alt="Foto de perfil actual" class="foto-perfil">
            </div>

            <button type="submit" name="guardar_perfil">Guardar cambios</button>
        </form>
    </div>
</body>

</html>
