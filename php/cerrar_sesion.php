<?php
session_start();
include 'conexion_be.php';
require_once '../include/actividad.php';

// registrar cierre de sesión ANTES de destruirla
registrarActividad($conexion,
    'Cierre de sesión',
    "El usuario {$_SESSION['nombre']} ({$_SESSION['rol']}) cerró sesión."
);

$_SESSION = [];
session_destroy();
mysqli_close($conexion);
header("location: ../index.php");
exit();