<?php
require_once 'include/auth_agente.php';
include 'php/conexion_be.php';

$agente_id = $_SESSION['user_id'];

$stmt = $conexion->prepare("
    SELECT ci.*, u.nombre AS cliente, p.titulo AS propiedad
    FROM clientes_interesados ci
    JOIN usuarios u ON ci.cliente_id = u.id
    JOIN propiedades p ON ci.propiedad_id = p.id
    WHERE p.agente_id = :agente_id
    ORDER BY ci.fecha_registro DESC
");
$stmt->execute([':agente_id' => $agente_id]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes interesados — Lorent Inmobiliaria</title>
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Clientes interesados</span>
        </div>
        <div class="content">
            <table>
                <thead>
                    <tr><th>Cliente</th><th>Propiedad</th><th>Correo</th><th>Teléfono</th><th>Mensaje</th><th>Fecha</th></tr>
                </thead>
                <tbody>
                <?php if($stmt->rowCount()==0): ?>
                    <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay clientes interesados registrados aún.</td></tr>
                <?php else: ?>
                    <?php while($c=$stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['cliente']) ?></td>
                        <td><?= htmlspecialchars($c['propiedad']) ?></td>
                        <td><?= htmlspecialchars($c['correo']) ?></td>
                        <td><?= htmlspecialchars($c['telefono']) ?></td>
                        <td><?= htmlspecialchars($c['mensaje']) ?></td>
                        <td><?= $c['fecha_registro'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

