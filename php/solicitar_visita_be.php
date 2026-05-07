<?php
session_start();
if (!isset($_SESSION['usuario'])) { 
    header("location: ../index.php"); 
    exit(); 
}

include 'conexion_be.php';
require_once '../include/actividad.php';

$propiedad_id = (int)$_POST['propiedad_id'];
$cliente_id   = (int)$_SESSION['user_id'];
$fecha = $_POST['fecha'];   // ej: 2026-05-07
$hora  = $_POST['hora'];    // ej: 18:30

$fecha_solicitada = $fecha . ' ' . $hora; // "2026-05-07 18:30"

$telefono     = $_POST['telefono'];
$mensaje      = trim($_POST['mensaje']);

// Inserta la solicitud de visita con todos los datos
$stmt = $conexion->prepare("
    INSERT INTO solicitudes_visita (cliente_id, propiedad_id, fecha_solicitada, telefono, mensaje)
    VALUES (:cliente_id, :propiedad_id, :fecha_solicitada, :telefono, :mensaje)
");
if ($stmt->execute([
    ':cliente_id'      => $cliente_id,
    ':propiedad_id'    => $propiedad_id,
    ':fecha_solicitada'=> $fecha_solicitada, // aquí va fecha + hora
    ':telefono'        => $telefono,
    ':mensaje'         => $mensaje
])) {
    // Log de actividad
    $info = $conexion->query("SELECT titulo FROM propiedades WHERE id=$propiedad_id")->fetch(PDO::FETCH_ASSOC);
    $tituloPropiedad = $info['titulo'] ?? "ID $propiedad_id";

    registrarActividad($conexion, 'Solicitud de visita enviada',
        "Cliente solicitó visita para \"$tituloPropiedad\" el $fecha_solicitada.");

    header("location: ../ver_propiedades.php?msg=visita_ok");
    exit();
} else {
    echo '<script>alert("Error al enviar la solicitud.");window.history.back();</script>';
}

?>
