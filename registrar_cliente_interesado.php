<?php
require_once 'include/auth_check.php';
include 'php/conexion_be.php';

// Solo agentes pueden acceder
if ($_SESSION['rol'] !== 'agente') {
    header("Location: dashboard_usuario.php");
    exit();
}

$propiedad_id = $_GET['propiedad_id'] ?? null;
$cliente_id   = $_GET['cliente_id'] ?? null;

$cliente = null;
if ($cliente_id) {
    $stmt = $conexion->prepare("SELECT nombre, correo, telefono FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
}

$mensaje = null;
if ($cliente_id && $propiedad_id) {
    $stmtMsg = $conexion->prepare("
        SELECT mensaje 
        FROM solicitudes_visita 
        WHERE cliente_id = :cliente_id AND propiedad_id = :propiedad_id
        ORDER BY fecha_solicitada DESC LIMIT 1
    ");
    $stmtMsg->execute([
        ':cliente_id' => $cliente_id,
        ':propiedad_id' => $propiedad_id
    ]);
    $mensaje = $stmtMsg->fetchColumn();
}

$mensaje = null;
$telefono = null;
if ($cliente_id && $propiedad_id) {
    $stmtMsg = $conexion->prepare("
        SELECT mensaje, telefono
        FROM solicitudes_visita 
        WHERE cliente_id = :cliente_id AND propiedad_id = :propiedad_id
        ORDER BY fecha_solicitada DESC LIMIT 1
    ");
    $stmtMsg->execute([
        ':cliente_id' => $cliente_id,
        ':propiedad_id' => $propiedad_id
    ]);
    $row = $stmtMsg->fetch(PDO::FETCH_ASSOC);
    $mensaje  = $row['mensaje'] ?? null;
    $telefono = $row['telefono'] ?? null;
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar cliente interesado — Lorent Inmobiliaria</title>
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
</head>
<body>
<div class="layout">
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Registrar cliente interesado</span>
        </div>

        <div class="content">
            <form method="POST" action="php/registrar_cliente_interesado_be.php" class="form-card">

                <!-- Campo oculto para pasar el cliente_id y prpiedad id-->
                <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($cliente_id ?? '') ?>">
                <input type="hidden" name="propiedad_id" value="<?= htmlspecialchars($propiedad_id ?? '') ?>">

                <label>Nombre del cliente</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>" required>

                <label>Correo electrónico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($cliente['correo'] ?? '') ?>" required>

                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($telefono ?? $cliente['telefono'] ?? '') ?>" required>

                <label>Mensaje del cliente</label>
                <textarea name="mensaje" rows="3"><?= htmlspecialchars($mensaje ?? '') ?></textarea>

                <button type="submit">Registrar cliente</button>
            </form>

        </div>
    </div>
</div>
</body>
</html>
