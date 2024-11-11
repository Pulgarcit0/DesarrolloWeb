$(document).ready(function() {
    let notificationCount = 0;

    function updateNotificationCount(message) {
        notificationCount++;
        $("#notificationCount").text(notificationCount);
        alert(message);
    }

    $("#menuIcon").click(function(e) {
        e.preventDefault();
        $("#menuDropdown").toggle();
    });

    $("#showCategories").click(function(e) {
        e.preventDefault();
        $("#mainMenu").hide();
        $("#categorySubmenu").show();
    });

    $("#showReport").click(function(e) {
        e.preventDefault();
        $("#mainMenu").hide();
        $("#reportSubmenu").show();
    });

    $("#exitcategotia").click(function(e) {
        e.preventDefault();
        $("#categorySubmenu").hide();
        $("#mainMenu").show();
    });

    $("#exitReportMenu").click(function(e) {
        e.preventDefault();
        $("#reportSubmenu").hide();
        $("#mainMenu").show();
    });

    $(".subcategory").click(function(e) {
        e.preventDefault();
        $(".subcategory").removeClass("selected");
        $(this).addClass("selected");

        let selectedCategory = $(this).text();
        updateNotificationCount(`Categoría "${selectedCategory}" seleccionada`);
    });

    $("#generateReportBtn").click(function() {
        const startDate = $("#startDate").val();
        const endDate = $("#endDate").val();

        if (startDate && endDate) {
            updateNotificationCount(`Reporte generado desde ${startDate} hasta ${endDate}`);
        } else {
            alert("Por favor, selecciona ambas fechas.");
        }
    });

    $(".category-buttons .btn-success").click(function() {
        let selectedCategory = $(".subcategory.selected").text();
        if (selectedCategory) {
            updateNotificationCount(`Categoría "${selectedCategory}" agregada`);
        } else {
            alert("Por favor, selecciona una categoría antes de agregarla.");
        }
    });

    $(".category-buttons .btn-danger").click(function() {
        let selectedCategory = $(".subcategory.selected").text();
        if (selectedCategory) {
            updateNotificationCount(`Categoría "${selectedCategory}" eliminada`);
            $(".subcategory.selected").removeClass("selected");
        } else {
            alert("Por favor, selecciona una categoría antes de eliminarla.");
        }
    });
});
