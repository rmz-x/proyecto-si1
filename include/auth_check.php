<?php
/* Incluir al inicio de CADA página protegida */
// Inicia la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, muestra alerta y redirige al login
    echo '<script>alert("Debes iniciar sesión primero.");window.location="index.php";</script>';
    exit();
}
