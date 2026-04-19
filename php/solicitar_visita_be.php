<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: ../index.php"); exit(); }
include 'conexion_be.php';
require_once '../include/actividad.php';

$propiedad_id = (int)$_POST['propiedad_id'];
$cliente_id   = (int)$_SESSION['user_id'];
$fecha        = $_POST['fecha'];
$mensaje      = trim($_POST['mensaje']);

$info = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT titulo FROM propiedades WHERE id=$propiedad_id"));
$tituloPropiedad = $info['titulo'] ?? "ID $propiedad_id";

$stmt = $conexion->prepare(
    "INSERT INTO solicitudes_visita (propiedad_id, cliente_id, fecha_solicitada, mensaje) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("iiss", $propiedad_id, $cliente_id, $fecha, $mensaje);

if ($stmt->execute()) {
    registrarActividad($conexion, 'Solicitud de visita enviada',
        "Cliente solicitó visita para \"$tituloPropiedad\" el $fecha.");
    header("location: ../ver_propiedades.php?msg=visita_ok");
} else {
    echo '<script>alert("Error al enviar la solicitud.");window.history.back();</script>';
}
$stmt->close();
mysqli_close($conexion);