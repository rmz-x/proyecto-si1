<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión.");window.location="index.php";</script>';
    exit();
}
if ($_SESSION['rol'] !== 'administrador') {
    echo '<script>alert("No tienes permiso para acceder a esta sección.");window.location="dashboard_usuario.php";</script>';
    exit();
}