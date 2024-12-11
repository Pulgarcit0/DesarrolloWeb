<?php
include_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre, apellido, email, contraseña, fecha_registro) VALUES (:nombre, :apellido, :email, :password, CURDATE())");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = '../Login.html';</script>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>alert('El correo ya está registrado.'); window.location.href = '../src/register.html';</script>";
        } else {
            die("Error: " . $e->getMessage());
        }
    }
}
?>
