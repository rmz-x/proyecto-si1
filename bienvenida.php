<?php 
    session_start();
    if(!isset($_SESSION['usuario'])){
        echo '
            <script>
                alert("Por favor debes iniciar sesion");
                window.location = "../index.php";
            </script>
        ';
        session_destroy();
        die();
    }

    session_start();
    header("location: dashboard.php");
    exit();
?>

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
</html>