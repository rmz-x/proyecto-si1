<?php
// Verifica que sea agente o admin
require_once 'include/auth_agente.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

$agente_id     = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// Clientes que solicitaron visita a propiedades de este agente
$clientes = $conexion->query(
    "SELECT DISTINCT u.id, u.nombre, u.correo, u.usuario,
            COUNT(sv.id) AS total_visitas,
            MAX(sv.fecha_solicitada) AS ultima_visita
     FROM solicitudes_visita sv
     JOIN propiedades p ON sv.propiedad_id = p.id
     JOIN usuarios u    ON sv.cliente_id   = u.id
     WHERE p.agente_id = $agente_id
     GROUP BY u.id
     ORDER BY ultima_visita DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes — Lorent</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Mis clientes</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
            </div>
        </div>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        Clientes que solicitaron visitas
                        <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">(<?= $clientes->rowCount() ?> clientes)</span>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr><th>#</th><th>Nombre</th><th>Correo</th><th>Usuario</th><th>Total visitas</th><th>Última solicitud</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                    <?php if($clientes->rowCount()==0): ?>
                        <tr><td colspan="7" style="text-align:center;color:#6c757d;padding:20px">Aún no tienes clientes con solicitudes.</td></tr>
                    <?php else: ?>
                        <?php while($c=$clientes->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                            <td><?= htmlspecialchars($c['correo']) ?></td>
                            <td><?= htmlspecialchars($c['usuario']) ?></td>
                            <td style="text-align:center">
                                <span class="badge badge-blue"><?= $c['total_visitas'] ?></span>
                            </td>
                            <td><?= $c['ultima_visita'] ?></td>
                            <td>
                                <a href="visitas_agente.php" class="btn-edit">Ver visitas</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>