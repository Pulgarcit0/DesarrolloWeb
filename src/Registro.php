<?php
// Incluir la conexión a la base de datos
include_once '../db_connection.php';

// Manejo del registro vía AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Validar si el email ya existe
        $stmt = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'El correo ya está registrado.']);
        } else {
            // Insertar el nuevo usuario en la base de datos
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare(
                "INSERT INTO Usuarios (nombre, apellido, email, contraseña, fecha_registro) 
                 VALUES (:nombre, :apellido, :email, :password, CURDATE())"
            );
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Registro exitoso.']);
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
    <title>Ahorrify - Registro</title>
    <link rel="stylesheet" href="../CSS/StylesLogin.css">
    <link rel="stylesheet" href="../CSS/fontello.css">
    <link rel="stylesheet" href="../font">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <div class="login-box">
        <h1>Registro - Ahorrify</h1>
        <div class="icon">
            <img src="../Imagenes/icono.png" alt="wallet icon">
        </div>
        <form id="registerForm" onsubmit="return false;">
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
            <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>
            <input type="email" id="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <button type="button" onclick="registerUser()">Registrarse</button>
        </form>
        <div id="registerError" class="error-message" style="display:none;"></div>
        <div id="registerSuccess" class="success-message" style="display:none;"></div>
        <div class="social-icons">
            <a href="https://x.com/HugolinoVa97500" class="icon-instagram"></a>
            <a href="https://x.com/HugolinoVa97500" class="icon-facebook-squared"></a>
            <a href="https://x.com/HugolinoVa97500" class="icon-twitter"></a>
            <a href="https://x.com/HugolinoVa97500" class="icon-youtube-play"></a>
        </div>
    </div>
</div>

<script>
    function registerUser() {
        const nombre = document.getElementById("nombre").value.trim();
        const apellido = document.getElementById("apellido").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const errorDiv = document.getElementById("registerError");
        const successDiv = document.getElementById("registerSuccess");

        if (password.length < 8) {
            errorDiv.style.display = "block";
            errorDiv.innerText = "La contraseña debe tener al menos 8 caracteres.";
            successDiv.style.display = "none";
            return;
        }

        // Enviar datos al servidor mediante AJAX
        $.ajax({
            url: "", // La misma página procesará el formulario
            type: "POST",
            dataType: "json",
            data: { nombre: nombre, apellido: apellido, email: email, password: password },
            success: function(response) {
                if (response.success) {
                    successDiv.style.display = "block";
                    successDiv.innerText = response.message;
                    errorDiv.style.display = "none";

                    // Redirigir al login después de unos segundos
                    setTimeout(() => {
                        window.location.href = "../src/Login.php";
                    }, 2000);
                } else {
                    errorDiv.style.display = "block";
                    errorDiv.innerText = response.message;
                    successDiv.style.display = "none";
                }
            },
            error: function(xhr, status, error) {
                errorDiv.style.display = "block";
                errorDiv.innerText = "Error al conectar con el servidor.";
                successDiv.style.display = "none";
            }
        });
    }
</script>
</body>
</html>
