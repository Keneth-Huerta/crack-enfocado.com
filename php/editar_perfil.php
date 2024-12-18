<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../php/conexion.php'; // Conexión a la base de datos

// Asegúrate de que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "Por favor, inicie sesión para continuar.";
    exit();
}

// Obtener el usuario_id de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Obtener la información del usuario
$query = "SELECT * FROM perfiles WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Verificar si el perfil existe
if ($perfil === null) {
    // Si no existe el perfil, inicializamos los valores en blanco
    $perfil = [
        'nombre' => '',
        'apellido' => '',
        'carrera' => '',
        'semestre' => '',
        'foto_perfil' => '',
        'foto_portada' => '',
        'informacion_extra' => ''
    ];
}

// Procesar los cambios del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $carrera = $_POST['carrera'];
    $semestre = $_POST['semestre'];
    $informacion_extra = $_POST['informacion_extra'];

    // Subir las nuevas fotos si se han proporcionado
    $foto_perfil = $_FILES['foto_perfil']['name'] ? '../media/uploads/' . basename($_FILES['foto_perfil']['name']) : $perfil['foto_perfil'];
    $foto_portada = $_FILES['foto_portada']['name'] ? '../media/uploads/' . basename($_FILES['foto_portada']['name']) : $perfil['foto_portada'];

    // Guardar las nuevas imágenes si se han cargado
    if ($_FILES['foto_perfil']['name']) {
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_perfil);
    }
    if ($_FILES['foto_portada']['name']) {
        move_uploaded_file($_FILES['foto_portada']['tmp_name'], $foto_portada);
    }

    // Si el perfil ya existe, actualizamos
    if ($perfil['usuario_id'] != '') {
        $update_query = "UPDATE perfiles SET nombre = ?, apellido = ?, carrera = ?, semestre = ?, foto_perfil = ?, foto_portada = ?, informacion_extra = ? WHERE usuario_id = ?";
        $stmt = mysqli_prepare($enlace, $update_query);

        // Asegúrate de que la cantidad de marcadores coincida con el número de parámetros
        mysqli_stmt_bind_param($stmt, "sssisssi", $nombre, $apellido, $carrera, $semestre, $foto_perfil, $foto_portada, $informacion_extra, $usuario_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // Insertar un nuevo perfil si no existe
        $insert_query = "INSERT INTO perfiles (usuario_id, nombre, apellido, carrera, semestre, foto_perfil, foto_portada, informacion_extra) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($enlace, $insert_query);

        // Asegúrate de que la cantidad de marcadores coincida con el número de parámetros
        mysqli_stmt_bind_param($stmt, "isssisss", $usuario_id, $nombre, $apellido, $carrera, $semestre, $foto_perfil, $foto_portada, $informacion_extra);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Redirigir al perfil después de guardar cambios
    header("Location: perfil.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar perfil</title>
    <link rel="stylesheet" href="../css/editar_perfil.css">
</head>

<body>
    <div class="form-container">
        <h1>Editar perfil</h1>
        <form method="POST" action="editar_perfil.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($perfil['nombre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($perfil['apellido'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera:</label>
                <select id="carrera" name="carrera" required>
                    <option value="Técnico en Aeronáutica" <?php echo ($perfil['carrera'] == 'Técnico en Aeronáutica') ? 'selected' : ''; ?>>Técnico en Aeronáutica</option>
                    <option value="Técnico en Computación" <?php echo ($perfil['carrera'] == 'Técnico en Computación') ? 'selected' : ''; ?>>Técnico en Computación</option>
                    <option value="Técnico en Manufactura Asistida por Computadora" <?php echo ($perfil['carrera'] == 'Técnico en Manufactura Asistida por Computadora') ? 'selected' : ''; ?>>Técnico en Manufactura Asistida por Computadora</option>
                    <option value="Técnico en Sistemas Automotrices" <?php echo ($perfil['carrera'] == 'Técnico en Sistemas Automotrices') ? 'selected' : ''; ?>>Técnico en Sistemas Automotrices</option>
                    <option value="Técnico en Sistemas Digitales" <?php echo ($perfil['carrera'] == 'Técnico en Sistemas Digitales') ? 'selected' : ''; ?>>Técnico en Sistemas Digitales</option>
                </select>
            </div>

            <div class="form-group">
                <label for="semestre">Semestre:</label>
                <input type="number" id="semestre" name="semestre" value="<?php echo htmlspecialchars($perfil['semestre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="foto_perfil">Foto de perfil:</label>
                <input type="file" id="foto_perfil" name="foto_perfil">
            </div>

            <div class="form-group">
                <label for="foto_portada">Foto de portada:</label>
                <input type="file" id="foto_portada" name="foto_portada">
            </div>

            <div class="form-group">
                <label for="informacion_extra">Información extra:</label>
                <textarea id="informacion_extra" name="informacion_extra"><?php echo htmlspecialchars($perfil['informacion_extra'] ?? ''); ?></textarea>
            </div>

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>

</html>