<?php
// Inicia la sesión para acceder a los datos del usuario
session_start();
// Incluye la conexión a la base de datos
include 'conexion_be.php';
// Incluye la función para registrar actividades
require_once '../include/actividad.php';

// Registra la actividad de cierre de sesión ANTES de destruir la sesión
registrarActividad($conexion,
    'Cierre de sesión',
    "El usuario {$_SESSION['nombre']} ({$_SESSION['rol']}) cerró sesión."
);

// Limpia todas las variables de sesión
$_SESSION = [];
// Destruye la sesión completamente
session_destroy();
// Redirige al login
header("location: ../index.php");
exit();