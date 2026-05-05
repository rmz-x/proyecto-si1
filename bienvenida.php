<?php
// Inicia la sesión para verificar si el usuario está logueado
session_start();
// Verifica si la variable de sesión 'usuario' existe
if(!isset($_SESSION['usuario'])){
    // Si no está logueado, muestra alerta y redirige al login
    echo '
        <script>
            alert("Por favor debes iniciar sesion");
            window.location = "../index.php";
        </script>
    ';
    // Destruye la sesión y termina el script
    session_destroy();
    die();
}

// Si está logueado, inicia sesión nuevamente (redundante, pero por consistencia)
session_start();
// Redirige al dashboard principal
header("location: dashboard.php");
exit();
?>

<!-- Código HTML comentado (antigua versión de la página, no se usa) -->
<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bienvenida</title>
</head>
<body>
    <h1>hacer el backen</h1>
    <a href="php/cerrar_sesion.php">Cerrar sesion</a>
</body>
</html>-->