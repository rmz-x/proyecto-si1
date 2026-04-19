<?php
/* Incluir al inicio de CADA página protegida */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión primero.");window.location="index.php";</script>';
    exit();
}
