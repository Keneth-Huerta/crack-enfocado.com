
<?php
$servor = "localhost";
$usuarip = "root";
$clave = "";
$baseDeDatos = "Usuarios";


$enlace = mysqli_connect($servor, $usuarip, $clave, $baseDeDatos);


if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}


$correo = $_POST['correo'];
$contra = $_POST['contra'];


$query = "SELECT * FROM registro WHERE correo = '$correo' AND contra = '$contra'";
$resultado = mysqli_query($enlace, $query);


if (mysqli_num_rows($resultado) > 0) {
   
    session_start();
    
    $fila = mysqli_fetch_assoc($resultado);
    header("Location: ../usuario.html");
    exit();
} else{
    echo ' <script>
    alert("Usuario o contraseña son invalidos");
    location.href="../formulario2.html";
 </script>';
}


mysqli_free_result($resultado);
mysqli_close($enlace);
?>
