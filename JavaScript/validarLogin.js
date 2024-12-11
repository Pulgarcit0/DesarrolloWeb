function validateForm() {
    const password = document.getElementById("password").value;

    if (password.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        return false;
    }
    
    // Redirigir a dashboard.html si la validación es exitosa
    window.location.href = "Principal.php";

    return false; // Evita el envío del formulario
}
