<?php
// Inicia sesión
session_start();
// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) { header("location: ../index.php"); exit(); }
// Incluye conexión a BD
include 'conexion_be.php';
// Incluye función para registrar actividad
require_once '../include/actividad.php';

// Obtiene datos del formulario
$accion      = $_POST['accion']      ?? 'registrar';
$titulo      = trim($_POST['titulo']      ?? '');
$tipo        = $_POST['tipo']        ?? 'Venta';
$zona        = trim($_POST['zona']        ?? '');
$precio      = $_POST['precio']      ?? 0;
$area        = $_POST['area']        !== '' ? $_POST['area'] : null;
$descripcion = trim($_POST['descripcion'] ?? '');
$estado      = $_POST['estado']      ?? 'Disponible';
$agente_id   = !empty($_POST['agente_id']) ? (int)$_POST['agente_id'] : null;

if ($accion === 'registrar') {
    $stmt = $conexion->prepare(
        "INSERT INTO propiedades (titulo, tipo, zona, precio, area, descripcion, estado, agente_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt->execute([$titulo, $tipo, $zona, $precio, $area, $descripcion, $estado, $agente_id])) {
        registrarActividad($conexion, 'Propiedad registrada',
            "Se registró la propiedad: \"$titulo\" ($tipo) en $zona por \$$precio.");
        echo '<script>alert("Propiedad registrada exitosamente.");window.location="../propiedades.php";</script>';
    } else {
        echo '<script>alert("Error al registrar la propiedad.");window.location="../propiedades.php";</script>';
    }

} elseif ($accion === 'modificar') {
    $id = (int)$_POST['id'];
    $stmt = $conexion->prepare(
        "UPDATE propiedades SET titulo=?, tipo=?, zona=?, precio=?, area=?, descripcion=?, estado=?, agente_id=? WHERE id=?"
    );

    if ($stmt->execute([$titulo, $tipo, $zona, $precio, $area, $descripcion, $estado, $agente_id, $id])) {
        registrarActividad($conexion, 'Propiedad modificada',
            "Se modificó la propiedad ID $id: \"$titulo\" — nuevo estado: $estado.");
        echo '<script>alert("Propiedad actualizada correctamente.");window.location="../propiedades.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar la propiedad.");window.location="../propiedades.php";</script>';
    }
}