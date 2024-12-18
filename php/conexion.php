<?php
$host = 'localhost';     // O la dirección de tu servidor de base de datos
$db   = 'nombre_de_base_de_datos'; // El nombre de tu base de datos
$user = 'usuario';       // El nombre de usuario de la base de datos
$pass = 'contraseña';    // La contraseña de la base de datos
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
