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

// Obtener los datos del usuario
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
        $usuario = $fila['username'];
        $correo = $fila['correo'];
        $nombre = $fila['nombre'];
        $apellido = $fila['apellido'];
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo $usuario; ?></title>
    <link rel="stylesheet" href="../css/misestilos.css">
</head>

<body>
    <div class="form-container">
        <h1>Mi Perfil</h1>
        
        <div class="perfil-info">
            <img src="uploads/<?php echo $foto_perfil; ?>" alt="Foto de perfil" class="foto-perfil">

            <form action="php/editar_perfil.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
                </div>

                <div class="form-group">
                    <label for="carrera">Carrera:</label>
                    <select name="carrera" id="carrera" required>
                        <option value="Técnico en Aeronáutica" <?php echo ($carrera == "Técnico en Aeronáutica") ? "selected" : ""; ?>>Técnico en Aeronáutica</option>
                        <option value="Técnico en Computación" <?php echo ($carrera == "Técnico en Computación") ? "selected" : ""; ?>>Técnico en Computación</option>
                        <option value="Técnico en Manufactura Asistida por Computadora" <?php echo ($carrera == "Técnico en Manufactura Asistida por Computadora") ? "selected" : ""; ?>>Técnico en Manufactura Asistida por Computadora</option>
                        <option value="Técnico en Sistemas Automotrices" <?php echo ($carrera == "Técnico en Sistemas Automotrices") ? "selected" : ""; ?>>Técnico en Sistemas Automotrices</option>
                        <option value="Técnico en Sistemas Digitales" <?php echo ($carrera == "Técnico en Sistemas Digitales") ? "selected" : ""; ?>>Técnico en Sistemas Digitales</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semestre">Semestre:</label>
                    <input type="number" name="semestre" id="semestre" value="<?php echo htmlspecialchars($semestre); ?>" required>
                </div>

                <div class="form-group">
                    <label for="informacion_extra">Información adicional:</label>
                    <textarea name="informacion_extra" id="informacion_extra" rows="4"><?php echo htmlspecialchars($informacion_extra); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="foto_perfil">Cambiar foto de perfil:</label>
                    <input type="file" name="foto_perfil" id="foto_perfil">
                </div>

                <button type="submit" name="guardar_perfil">Guardar cambios</button>
            </form>
        </div>

        <div class="logout-link">
            <a href="php/logout.php">Cerrar sesión</a>
        </div>
    </div>
</body>

</html>
