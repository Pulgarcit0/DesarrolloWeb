<?php
// Incluir conexión a la base de datos
include_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Consulta para obtener al usuario
        $stmt = $conn->prepare("SELECT id_usuario, contraseña FROM Usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['contraseña'])) {
            // Login exitoso
            session_start();
            $_SESSION['user_id'] = $user['id_usuario'];

            // Redirigir al dashboard
            header("Location: ../Dashboard.html");
            exit();
        } else {
            // Credenciales incorrectas
            echo "<script>alert('Correo o contraseña incorrectos'); window.location.href = '../Login.html';</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirigir al login si no es una solicitud POST
    header("Location: ../Login.html");
    exit();
}
?>
