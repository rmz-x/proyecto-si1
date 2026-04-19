<?php

function registrarActividad($conexion, $accion, $descripcion = '') {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $usuario_id  = $_SESSION['user_id'] ?? null;
    $nombre      = $_SESSION['nombre']  ?? null;
    $correo      = $_SESSION['usuario'] ?? null;
    $rol         = $_SESSION['rol']     ?? null;
    $ip          = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $conexion->prepare(
        "INSERT INTO registro_actividad (usuario_id, nombre, correo, rol, accion, descripcion, ip)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issssss",
        $usuario_id, $nombre, $correo, $rol, $accion, $descripcion, $ip
    );
    $stmt->execute();
    $stmt->close();
}