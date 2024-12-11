<?php
// Incluir lógica para categorías y reportes
include 'category_logic.php';
include 'report_logic.php';
?>

<div id="overlay"></div>

<!-- Modal de Categorías -->
<div id="categoryModal">
    <button class="close-btn" id="closeCategoryModal">&times;</button>
    <h4>Gestión de Categorías</h4>
    <?php include 'category_modal.php'; ?>
</div>

<!-- Modal de Reporte Financiero -->
<div id="reportModal">
    <button class="close-btn" id="closeReportModal">&times;</button>
    <h4>Generar Reporte Financiero</h4>
    <?php include 'report_modal.php'; ?>
</div>
