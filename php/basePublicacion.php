
<?php
$host = 'localhost';
$dbname = 'u288355303_Usuarios';
$username = 'u288355303_Keneth';
$password = '1420Genio.';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
