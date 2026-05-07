<?php
require_once '../include/auth_check.php';
include 'conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $estado = $_POST['estado'];

    // Actualizar estado del prospecto en la tabla principal
    $stmt = $conexion->prepare("UPDATE prospectos SET estado = :estado WHERE id = :id");
    if ($stmt->execute([':estado' => $estado, ':id' => $id])) {
        echo '<script>alert("Prospecto actualizado correctamente.");window.location="../prospectos_crm.php";</script>';
    } else {
        echo '<script>alert("Error al guardar cambios.");window.location="../prospectos_crm.php";</script>';
    }
}
?>
