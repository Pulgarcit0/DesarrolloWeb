<?php
$host = "localhost";
$username = "root";
$password = "toor"; // Cambia esto si tienes una contraseña configurada
$database = "gestion_financiera";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
