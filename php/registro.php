<?php
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";
if (isset($_POST['registrar'])) {
    $usuario = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $boleta = $_POST['boleta'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $insertarDatos = "INSERT INTO registro VALUES ('','$usuario','$apellido','$boleta','$correo','$contraseña')";
    $ejecutarInser = mysqli_query($enlace, $insertarDatos);
    echo ' <script>
    location.href="../formulario2.html";
 </script>';
}
