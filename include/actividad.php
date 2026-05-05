<?php

// Función para registrar actividades de los usuarios en la base de datos
function registrarActividad($conexion, $accion, $descripcion = '') {
    // Inicia la sesión si no está activa (por si acaso)
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Obtiene los datos del usuario de la sesión
    $usuario_id  = $_SESSION['user_id'] ?? null;  // ID del usuario
    $nombre      = $_SESSION['nombre']  ?? null;  // Nombre del usuario
    $correo      = $_SESSION['usuario'] ?? null;  // Correo del usuario
    $rol         = $_SESSION['rol']     ?? null;  // Rol del usuario
    $ip          = $_SERVER['REMOTE_ADDR'] ?? null;  // IP del usuario

    // Prepara la consulta para insertar la actividad en la tabla registro_actividad
    $stmt = $conexion->prepare(
        "INSERT INTO registro_actividad (usuario_id, nombre, correo, rol, accion, descripcion, ip)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    // Ejecuta la consulta con los valores obtenidos
    $stmt->execute([$usuario_id, $nombre, $correo, $rol, $accion, $descripcion, $ip]);
}