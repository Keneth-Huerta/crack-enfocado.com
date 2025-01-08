/**
 * Establishes a connection to the MySQL database using PDO.
 *
 * Configuration:
 * - Host: localhost
 * - Database Name: u288355303_Usuarios
 * - Username: u288355303_Keneth
 * - Password: 1420Genio.
 *
 * Attributes:
 * - Sets the PDO error mode to exception.
 *
 * Error Handling:
 * - If the connection fails, it catches the PDOException and terminates the script with an error message.
 *
 * @throws PDOException if the connection to the database fails.
 */
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
