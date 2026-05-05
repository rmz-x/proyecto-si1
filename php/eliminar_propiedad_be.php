<?php
// Inicia sesión
session_start();
// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) { header("location: ../index.php"); exit(); }
// Incluye conexión a BD
include 'conexion_be.php';
// Incluye función para registrar actividad
require_once '../include/actividad.php';

// Obtiene ID de la propiedad desde GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '<script>alert("ID inválido.");window.location="../propiedades.php";</script>';
    exit();
}

// Guardar el título antes de eliminar para el log
$info = $conexion->query("SELECT titulo FROM propiedades WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
$titulo = $info['titulo'] ?? "ID $id";

// Elimina la propiedad
$stmt = $conexion->prepare("DELETE FROM propiedades WHERE id = ?");

if ($stmt->execute([$id])) {
    registrarActividad($conexion, 'Propiedad eliminada',
        "Se eliminó la propiedad: \"$titulo\" (ID $id).");

    // Redirigir según rol
    $destino = in_array($_SESSION['rol'], ['administrador']) ? '../propiedades.php' : '../propiedades_agente.php';
    echo "<script>alert('Propiedad eliminada correctamente.');window.location='$destino';</script>";
} else {
    echo '<script>alert("Error al eliminar la propiedad.");window.history.back();</script>';
}
?>
