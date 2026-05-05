<?php
// Inicia la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) session_start();
// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, muestra alerta y redirige al login
    echo '<script>alert("Debes iniciar sesión.");window.location="index.php";</script>'; exit();
}
// Verifica si el usuario tiene rol de agente o administrador
if (!in_array($_SESSION['rol'], ['agente', 'administrador'])) {
    // Si no tiene permiso, muestra alerta y redirige al login
    echo '<script>alert("No tienes permiso para acceder a esta sección.");window.location="index.php";</script>'; exit();
}