<?php
// Habilitar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar el buffer de salida
ob_start();

// Incluir conexión a la base de datos
include_once '../db_connection.php';

// Procesar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Agregar gasto
    if ($accion === 'agregar_gasto') {
        $monto = $_POST['monto_gasto'];
        $categoria = $_POST['categoria_gasto'];
        $fecha = date('Y-m-d');
        try {
            $stmt = $conn->prepare("INSERT INTO Pagos (monto_pagado, id_tipo_gasto, fecha_pago) VALUES (?, ?, ?)");
            $stmt->execute([$monto, $categoria, $fecha]);
        } catch (PDOException $e) {
            die("Error al agregar el gasto: " . $e->getMessage());
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Agregar ingreso
    if ($accion === 'agregar_ingreso') {
        $monto = $_POST['monto_ingreso'];
        $categoria = $_POST['categoria_ingreso'];
        $fecha = date('Y-m-d');
        try {
            $stmt = $conn->prepare("INSERT INTO Pagos (monto_pagado, id_tipo_gasto, fecha_pago) VALUES (?, ?, ?)");
            // Guardar ingreso como negativo
            $stmt->execute([-abs($monto), $categoria, $fecha]);
        } catch (PDOException $e) {
            die("Error al agregar el ingreso: " . $e->getMessage());
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Crear categoría
    if ($accion === 'crear') {
        $nombre = $_POST['nombre_categoria'];
        $descripcion = $_POST['descripcion'] ?? null;
        try {
            $stmt = $conn->prepare("INSERT INTO Tipos_De_Gasto (nombre_tipo, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre, $descripcion]);
        } catch (PDOException $e) {
            die("Error al agregar categoría: " . $e->getMessage());
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Eliminar categoría
    if ($accion === 'eliminar') {
        $id = $_POST['id_categoria'];
        try {
            $stmt = $conn->prepare("DELETE FROM Tipos_De_Gasto WHERE id_tipo_gasto = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            die("Error al eliminar categoría: " . $e->getMessage());
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Generar reporte financiero
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'generar_reporte') {
        $fechaInicio = $_POST['fecha_inicio'];
        $fechaFin = $_POST['fecha_fin'];
        $nombreUsuario = 'Usuario Logueado'; // Reemplaza con el nombre del usuario logueado (obtén de sesión o BD)

        try {
            // Consulta para obtener datos
            $stmtReporte = $conn->prepare("
            SELECT p.fecha_pago, p.monto_pagado, tdg.nombre_tipo AS categoria, 
                   CASE 
                       WHEN p.monto_pagado > 0 THEN 'Gasto' 
                       ELSE 'Ingreso' 
                   END AS tipo
            FROM Pagos p
            INNER JOIN Tipos_De_Gasto tdg ON p.id_tipo_gasto = tdg.id_tipo_gasto
            WHERE p.fecha_pago BETWEEN ? AND ?
            ORDER BY p.fecha_pago ASC
        ");
            $stmtReporte->execute([$fechaInicio, $fechaFin]);
            $reportData = $stmtReporte->fetchAll(PDO::FETCH_ASSOC);

            // Calcular totales
            $totalGastos = 0;
            $totalIngresos = 0;
            foreach ($reportData as $row) {
                if ($row['tipo'] === 'Gasto') {
                    $totalGastos += $row['monto_pagado'];
                } else {
                    $totalIngresos += abs($row['monto_pagado']);
                }
            }

            // Guardar reporte en el historial
            try {
                $stmtGuardarReporte = $conn->prepare("
                INSERT INTO Historial_Reportes (usuario, fecha_inicio, fecha_fin, total_gastos, total_ingresos, balance_neto)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
                $balanceNeto = $totalIngresos - $totalGastos;
                $stmtGuardarReporte->execute([
                    $nombreUsuario,
                    $fechaInicio,
                    $fechaFin,
                    $totalGastos,
                    $totalIngresos,
                    $balanceNeto
                ]);
            } catch (PDOException $e) {
                die("Error al guardar el historial del reporte: " . $e->getMessage());
            }

            // Generar PDF
            require_once('../fpdf/fpdf.php');
            ob_end_clean();

            $pdf = new FPDF();
            $pdf->AddPage();

            // Encabezado
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Reporte Financiero', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Generado por: ' . $nombreUsuario, 0, 1, 'C');
            $pdf->Cell(0, 10, 'Desde: ' . $fechaInicio . ' Hasta: ' . $fechaFin, 0, 1, 'C');
            $pdf->Ln(10);

            // Sección de Gastos
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'Gastos', 0, 1);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(40, 10, 'Fecha', 1);
            $pdf->Cell(70, 10, 'Categoría', 1);
            $pdf->Cell(40, 10, 'Monto', 1, 0, 'R');
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 12);
            foreach ($reportData as $row) {
                if ($row['tipo'] === 'Gasto') {
                    $pdf->Cell(40, 10, $row['fecha_pago'], 1);
                    $pdf->Cell(70, 10, $row['categoria'], 1);
                    $pdf->Cell(40, 10, number_format($row['monto_pagado'], 2), 1, 0, 'R');
                    $pdf->Ln();
                }
            }
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(110, 10, 'Total Gastos', 1);
            $pdf->Cell(40, 10, number_format($totalGastos, 2), 1, 0, 'R');
            $pdf->Ln(20);

            // Sección de Ingresos
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'Ingresos', 0, 1);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(40, 10, 'Fecha', 1);
            $pdf->Cell(70, 10, 'Categoría', 1);
            $pdf->Cell(40, 10, 'Monto', 1, 0, 'R');
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 12);
            foreach ($reportData as $row) {
                if ($row['tipo'] === 'Ingreso') {
                    $pdf->Cell(40, 10, $row['fecha_pago'], 1);
                    $pdf->Cell(70, 10, $row['categoria'], 1);
                    $pdf->Cell(40, 10, number_format(abs($row['monto_pagado']), 2), 1, 0, 'R');
                    $pdf->Ln();
                }
            }
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(110, 10, 'Total Ingresos', 1);
            $pdf->Cell(40, 10, number_format($totalIngresos, 2), 1, 0, 'R');
            $pdf->Ln(20);

            // Resumen Final
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'Resumen Financiero', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(110, 10, 'Total Gastos:', 1);
            $pdf->Cell(40, 10, number_format($totalGastos, 2), 1, 0, 'R');
            $pdf->Ln();
            $pdf->Cell(110, 10, 'Total Ingresos:', 1);
            $pdf->Cell(40, 10, number_format($totalIngresos, 2), 1, 0, 'R');
            $pdf->Ln();
            $pdf->Cell(110, 10, 'Balance Neto:', 1);
            $pdf->Cell(40, 10, number_format($totalIngresos - $totalGastos, 2), 1, 0, 'R');
            $pdf->Ln();

            $pdf->Output('I', 'Reporte_Financiero.pdf');

            exit();
        } catch (PDOException $e) {
            die("Error al generar el reporte financiero: " . $e->getMessage());
        }
    }

}

// Consultar datos para las gráficas y categorías
try {
    $stmtGastos = $conn->query("
        SELECT tdg.nombre_tipo AS categoria, SUM(p.monto_pagado) AS total
        FROM Pagos p
        INNER JOIN Tipos_De_Gasto tdg ON p.id_tipo_gasto = tdg.id_tipo_gasto
        WHERE p.monto_pagado > 0
        GROUP BY tdg.nombre_tipo
    ");
    $dataGastos = $stmtGastos->fetchAll(PDO::FETCH_ASSOC);

    $stmtIngresos = $conn->query("
        SELECT tdg.nombre_tipo AS categoria, SUM(p.monto_pagado) AS total
        FROM Pagos p
        INNER JOIN Tipos_De_Gasto tdg ON p.id_tipo_gasto = tdg.id_tipo_gasto
        WHERE p.monto_pagado < 0
        GROUP BY tdg.nombre_tipo
    ");
    $dataIngresos = $stmtIngresos->fetchAll(PDO::FETCH_ASSOC);

    $stmtCategorias = $conn->query("SELECT * FROM Tipos_De_Gasto");
    $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

$labelsGastos = array_column($dataGastos, 'categoria');
$valuesGastos = array_column($dataGastos, 'total');

$labelsIngresos = array_column($dataIngresos, 'categoria');
$valuesIngresos = array_map('abs', array_column($dataIngresos, 'total'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ahorify</title>
    <link rel="stylesheet" href="../CSS/stylePrincipal.css">
    <link rel="stylesheet" href="../CSS/StyleNotification.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<header>
    <div class="logo">
        <img src="../Imagenes/icono.png" alt="Logo" class="icon">
        <span>Ahorify</span>
    </div>
    <nav>
        <ul>
            <li><a href="#"><img src="../Imagenes/home.png" alt="Inicio"></a></li>
            <li><a href="#"><img src="../Imagenes/notificacion.webp" alt="Notificaciones"></a></li>
            <li><a href="#"><img src="../Imagenes/transferencia.webp" alt="Transferencias"></a></li>
            <li>
                <button id="menuToggle" class="btn btn-light" data-bs-toggle="dropdown">Menú</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCategorias">Categorías</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalReporte">Reporte Financiero</a></li>
                    <li><a class="dropdown-item" href="../src/educacion_financiera.html">Educación Financiera</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<main class="container mt-4">
    <!-- Gráfica de Gastos -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-center">Gráfica de Gastos</h5>
            <canvas id="gastosChart"></canvas>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalGasto">Agregar Gasto</button>
        </div>
    </div>

    <!-- Gráfica de Ingresos -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-center">Gráfica de Ingresos</h5>
            <canvas id="ingresosChart"></canvas>
            <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#modalIngreso">Agregar Ingreso</button>
        </div>
    </div>
</main>

<footer class="text-center mt-4">
    <p>&copy; 2024 Ahorify. Todos los derechos reservados.</p>
</footer>

<!-- Modal para Categorías -->
<div class="modal fade" id="modalCategorias" tabindex="-1" aria-labelledby="modalCategoriasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCategoriasLabel">Gestión de Categorías</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= htmlspecialchars($categoria['id_tipo_gasto']) ?></td>
                            <td><?= htmlspecialchars($categoria['nombre_tipo']) ?></td>
                            <td><?= htmlspecialchars($categoria['descripcion']) ?></td>
                            <td>
                                <form method="POST" action="dashboard.php">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_categoria" value="<?= $categoria['id_tipo_gasto'] ?>">
                                    <button class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="accion" value="crear">
                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre</label>
                        <input type="text" id="nombre_categoria" name="nombre_categoria" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Agregar Categoría</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Reporte Financiero -->
<div class="modal fade" id="modalReporte" tabindex="-1" aria-labelledby="modalReporteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="dashboard.php">
                <input type="hidden" name="accion" value="generar_reporte">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReporteLabel">Generar Reporte Financiero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Agregar Gasto -->
<div class="modal fade" id="modalGasto" tabindex="-1" aria-labelledby="modalGastoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="dashboard.php">
                <input type="hidden" name="accion" value="agregar_gasto">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGastoLabel">Agregar Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="monto_gasto" class="form-label">Monto:</label>
                        <input type="number" step="0.01" id="monto_gasto" name="monto_gasto" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_gasto" class="form-label">Categoría:</label>
                        <select id="categoria_gasto" name="categoria_gasto" class="form-control" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_tipo_gasto'] ?>"><?= htmlspecialchars($categoria['nombre_tipo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal para Agregar Ingreso -->
<div class="modal fade" id="modalIngreso" tabindex="-1" aria-labelledby="modalIngresoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="dashboard.php">
                <input type="hidden" name="accion" value="agregar_ingreso">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalIngresoLabel">Agregar Ingreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="monto_ingreso" class="form-label">Monto:</label>
                        <input type="number" step="0.01" id="monto_ingreso" name="monto_ingreso" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_ingreso" class="form-label">Categoría:</label>
                        <select id="categoria_ingreso" name="categoria_ingreso" class="form-control" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_tipo_gasto'] ?>"><?= htmlspecialchars($categoria['nombre_tipo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const gastosChart = new Chart(document.getElementById('gastosChart').getContext('2d'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Monto ($)', data: [], backgroundColor: 'rgba(220, 53, 69, 0.5)', borderColor: 'rgba(220, 53, 69, 1)', borderWidth: 1 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    const ingresosChart = new Chart(document.getElementById('ingresosChart').getContext('2d'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Monto ($)', data: [], backgroundColor: 'rgba(40, 167, 69, 0.5)', borderColor: 'rgba(40, 167, 69, 1)', borderWidth: 1 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    function updateCharts() {
        fetch('get_chart_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Actualizar datos de gastos
                const gastosLabels = data.gastos.map(gasto => gasto.categoria);
                const gastosValues = data.gastos.map(gasto => gasto.total);

                gastosChart.data.labels = gastosLabels;
                gastosChart.data.datasets[0].data = gastosValues;
                gastosChart.update();

                // Actualizar datos de ingresos
                const ingresosLabels = data.ingresos.map(ingreso => ingreso.categoria);
                const ingresosValues = data.ingresos.map(ingreso => Math.abs(ingreso.total));

                ingresosChart.data.labels = ingresosLabels;
                ingresosChart.data.datasets[0].data = ingresosValues;
                ingresosChart.update();
            })
            .catch(error => console.error('Error al cargar los datos:', error));
    }

    // Cargar datos al inicio
    updateCharts();

    // Actualizar cada vez que sea necesario
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', event => {
            setTimeout(updateCharts, 500); // Esperar a que el servidor procese antes de actualizar
        });
    });
</script>

</body>
</html>
