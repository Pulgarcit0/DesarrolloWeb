<?php
header('Content-Type: application/json');
include_once '../db_connection.php';

try {
    $stmtGastos = $conn->query("
        SELECT tdg.nombre_tipo AS categoria, SUM(p.monto_pagado) AS total
        FROM Pagos p
        INNER JOIN Tipos_De_Gasto tdg ON p.id_tipo_gasto = tdg.id_tipo_gasto
        WHERE p.monto_pagado > 0
        GROUP BY tdg.nombre_tipo
    ");
    $gastos = $stmtGastos->fetchAll(PDO::FETCH_ASSOC);

    $stmtIngresos = $conn->query("
        SELECT tdg.nombre_tipo AS categoria, SUM(p.monto_pagado) AS total
        FROM Pagos p
        INNER JOIN Tipos_De_Gasto tdg ON p.id_tipo_gasto = tdg.id_tipo_gasto
        WHERE p.monto_pagado < 0
        GROUP BY tdg.nombre_tipo
    ");
    $ingresos = $stmtIngresos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['gastos' => $gastos, 'ingresos' => $ingresos]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
