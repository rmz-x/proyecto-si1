<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: ../index.php"); exit(); }
include 'conexion_be.php';
require_once '../include/actividad.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '<script>alert("ID inválido.");window.location="../propiedades.php";</script>';
    exit();
}

// guardar el título antes de eliminar para el log
$info = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT titulo FROM propiedades WHERE id=$id"));
$titulo = $info['titulo'] ?? "ID $id";

$stmt = $conexion->prepare("DELETE FROM propiedades WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    registrarActividad($conexion, 'Propiedad eliminada',
        "Se eliminó la propiedad: \"$titulo\" (ID $id).");

    // redirigir según rol
    $destino = in_array($_SESSION['rol'], ['administrador']) ? '../propiedades.php' : '../propiedades_agente.php';
    echo "<script>alert('Propiedad eliminada correctamente.');window.location='$destino';</script>";
} else {
    echo '<script>alert("Error al eliminar la propiedad.");window.history.back();</script>';
}

$stmt->close();
mysqli_close($conexion);