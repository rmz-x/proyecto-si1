<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión.");window.location="index.php";</script>'; exit();
}
if (!in_array($_SESSION['rol'], ['asistente', 'administrador'])) {
    echo '<script>alert("No tienes permiso para acceder a esta sección.");window.location="index.php";</script>'; exit();
}