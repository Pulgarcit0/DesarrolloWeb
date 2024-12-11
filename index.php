<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahorify - Controla tu dinero</title>
    <link rel="stylesheet" href="CSS/StylesDashboard.css">
    <link rel="stylesheet" href="CSS/fontello.css">
    <link rel="stylesheet" href="font">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header>
    <div class="logo">
        <span>Ahorify</span>
    </div>
    <div class="auth-buttons">
        <a href="src/Registro.php" class="register">Regístrate</a>
        <a href="src/Login.php" class="login">Iniciar Sesión</a>
    </div>
</header>

<main class="intro-section">
    <div class="text-content">
        <h1>Controla tu dinero</h1>
        <p>Ahorify es una aplicación web que lleva un registro de tus gastos del día a día para obtener un control más fácil de tu dinero.</p>
    </div>
    <div class="image-content">
        <!-- Gráfico de criptomonedas -->
        <canvas id="cryptoChart" width="400" height="300"></canvas>
    </div>
</main>

<footer>
    <p>Contáctanos</p>
    <div class="social-icons">
        <a href="https://x.com/HugolinoVa97500" class="icon-instagram"></a>
        <a href="https://x.com/HugolinoVa97500" class="icon-facebook-squared"></a>
        <a href="https://x.com/HugolinoVa97500"  class="icon-twitter"></a>
        <a href="https://x.com/HugolinoVa97500" class="icon-youtube-play"></a>
    </div>
</footer>

<script>
    // Generar datos aleatorios para el gráfico
    function generateRandomData(points, min, max) {
        const data = [];
        for (let i = 0; i < points; i++) {
            data.push(Math.floor(Math.random() * (max - min + 1)) + min);
        }
        return data;
    }

    const labels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
    const randomData = generateRandomData(10, 1000, 5000);

    const data = {
        labels: labels,
        datasets: [{
            label: 'Incrementos ($)',
            data: randomData,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.4,
            fill: true,
            pointHoverRadius: 6,
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    title: {
                        display: true,
                        text: 'Ingreso ($)',
                    },
                    beginAtZero: false
                },
                x: {
                    title: {
                        display: true,
                        text: 'Días',
                    }
                }
            }
        }
    };

    // Inicializar el gráfico
    const ctx = document.getElementById('cryptoChart').getContext('2d');
    new Chart(ctx, config);
</script>
</body>
</html>
