<?php
// Incluir la conexión a la base de datos
include_once '../db_connection.php';

// Manejo del login vía AJAX
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
            echo json_encode(['success' => true, 'message' => 'Login exitoso']);
        } else {
            // Credenciales incorrectas
            echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahorrify - Login</title>
    <link rel="stylesheet" href="../CSS/StylesLogin.css">
    <link rel="stylesheet" href="../CSS/fontello.css">
    <link rel="stylesheet" href="../font">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <div class="login-box">
        <h1>Ahorrify</h1>
        <div class="icon">
            <img src="../Imagenes/icono.png" alt="wallet icon">
        </div>
        <form id="loginForm" onsubmit="return false;">
            <input type="text" id="username" name="username" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="button" onclick="validateLogin()">Acceso</button>
        </form>
        <div id="loginError" class="error-message" style="display:none;"></div>
        <div class="social-icons">
            <a href="https://x.com/HugolinoVa97500" class="icon-instagram"></a>
            <a href="https://x.com/HugolinoVa97500" class="icon-facebook-squared"></a>
            <a href="https://x.com/HugolinoVa97500" class="icon-twitter"></a>
            <a href="https://x.com/HugolinoVa97500" class="icon-youtube-play"></a>
        </div>
    </div>
</div>

<script>
    function validateLogin() {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const errorDiv = document.getElementById("loginError");

        if (password.length < 8) {
            errorDiv.style.display = "block";
            errorDiv.innerText = "La contraseña debe tener al menos 8 caracteres.";
            return;
        }

        // Enviar datos al servidor mediante AJAX
        $.ajax({
            url: "", // La misma página procesará el formulario
            type: "POST",
            dataType: "json",
            data: { username: username, password: password },
            success: function(response) {
                if (response.success) {
                    window.location.href = "dashboard.php";
                } else {
                    errorDiv.style.display = "block";
                    errorDiv.innerText = response.message;
                }
            },
            error: function(xhr, status, error) {
                errorDiv.style.display = "block";
                errorDiv.innerText = "Error al conectar con el servidor.";
            }
        });
    }
</script>
</body>
</html>
