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
$propiedad_id = (int)$_POST['propiedad_id'];
$cliente_id   = (int)$_SESSION['user_id'];
$fecha        = $_POST['fecha'];
$mensaje      = trim($_POST['mensaje']);

// Obtiene título de la propiedad para el log
$info = $conexion->query("SELECT titulo FROM propiedades WHERE id=$propiedad_id")->fetch(PDO::FETCH_ASSOC);
$tituloPropiedad = $info['titulo'] ?? "ID $propiedad_id";

// Inserta la solicitud de visita
$stmt = $conexion->prepare(
    "INSERT INTO solicitudes_visita (propiedad_id, cliente_id, fecha_solicitada, mensaje) VALUES (?, ?, ?, ?)"
);

if ($stmt->execute([$propiedad_id, $cliente_id, $fecha, $mensaje])) {
    registrarActividad($conexion, 'Solicitud de visita enviada',
        "Cliente solicitó visita para \"$tituloPropiedad\" el $fecha.");
    header("location: ../ver_propiedades.php?msg=visita_ok");
} else {
    echo '<script>alert("Error al enviar la solicitud.");window.history.back();</script>';
}
?>
