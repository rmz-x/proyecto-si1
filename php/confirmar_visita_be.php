<?php
// Inicia sesión
session_start();
// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("location: ../index.php"); exit();
}
// Incluye conexión a BD
include 'conexion_be.php';

// Obtiene ID de la solicitud y estado desde GET
$id     = (int)$_GET['id'];
$estado = $_GET['estado'] ?? '';

// Valida que el estado sea válido
$estados_validos = ['confirmada', 'cancelada'];
if (!in_array($estado, $estados_validos)) {
    echo '<script>alert("Acción no válida.");window.history.back();</script>'; exit();
}

// Actualiza el estado de la solicitud de visita
$stmt = $conexion->prepare("UPDATE solicitudes_visita SET estado = ? WHERE id = ?");

if ($stmt->execute([$estado, $id])) {
    $msg = $estado === 'confirmada' ? 'Visita confirmada.' : 'Visita cancelada.';
    echo "<script>alert('$msg');window.location='../dashboard_agente.php';</script>";
} else {
    echo '<script>alert("Error al actualizar.");window.history.back();</script>';
}
?>
