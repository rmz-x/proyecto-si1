<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("location: ../index.php"); exit();
}
include 'conexion_be.php';

$id     = (int)$_GET['id'];
$estado = $_GET['estado'] ?? '';

$estados_validos = ['confirmada', 'cancelada'];
if (!in_array($estado, $estados_validos)) {
    echo '<script>alert("Acción no válida.");window.history.back();</script>'; exit();
}

$stmt = $conexion->prepare("UPDATE solicitudes_visita SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    $msg = $estado === 'confirmada' ? 'Visita confirmada.' : 'Visita cancelada.';
    echo "<script>alert('$msg');window.location='../dashboard_agente.php';</script>";
} else {
    echo '<script>alert("Error al actualizar.");window.history.back();</script>';
}
$stmt->close();
mysqli_close($conexion);