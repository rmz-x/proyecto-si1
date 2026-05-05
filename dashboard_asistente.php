<?php
// Verifica que el usuario sea asistente o admin
require_once 'include/auth_asistente.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Obtiene nombre del usuario
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// Estadísticas generales para el asistente
$total_clientes  = $conexion->query("SELECT COUNT(*) AS n FROM usuarios WHERE rol='cliente'")->fetch(PDO::FETCH_ASSOC)['n'];
$visitas_pend    = $conexion->query("SELECT COUNT(*) AS n FROM solicitudes_visita WHERE estado='pendiente'")->fetch(PDO::FETCH_ASSOC)['n'];
$visitas_hoy     = $conexion->query("SELECT COUNT(*) AS n FROM solicitudes_visita WHERE fecha_solicitada=CURDATE()")->fetch(PDO::FETCH_ASSOC)['n'];
$total_props     = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible'")->fetch(PDO::FETCH_ASSOC)['n'];

// Últimas solicitudes de visita
$solicitudes = $conexion->query(
    "SELECT sv.*, p.titulo AS propiedad, u.nombre AS cliente
     FROM solicitudes_visita sv
     JOIN propiedades p ON sv.propiedad_id = p.id
     JOIN usuarios u    ON sv.cliente_id   = u.id
     ORDER BY sv.fecha_registro DESC LIMIT 8");

// Últimos clientes registrados
$clientes = $conexion->query(
    "SELECT id, nombre, correo, usuario FROM usuarios WHERE rol='cliente' ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Asistente — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Panel del Asistente</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge" style="background:#EEEDFE;color:#534AB7;font-size:11px;padding:2px 8px;border-radius:10px">Asistente</span>
            </div>
        </div>
        <div class="content">

            <div class="stats">
                <div class="stat-card">
                    <p class="stat-label">Clientes registrados</p>
                    <p class="stat-value"><?= $total_clientes ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Visitas pendientes</p>
                    <p class="stat-value"><?= $visitas_pend ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Visitas hoy</p>
                    <p class="stat-value"><?= $visitas_hoy ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Propiedades disponibles</p>
                    <p class="stat-value"><?= $total_props ?></p>
                </div>
            </div>

            <!-- agenda de visitas -->
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <span class="card-title">Agenda de solicitudes de visita</span>
                    <a href="visitas_asistente.php" class="btn-primary">Ver todas</a>
                </div>
                <table>
                    <thead>
                        <tr><th>Cliente</th><th>Propiedad</th><th>Fecha</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                    <?php if($solicitudes->rowCount()==0): ?>
                        <tr><td colspan="4" style="text-align:center;color:#6c757d;padding:20px">No hay solicitudes registradas.</td></tr>
                    <?php else: ?>
                        <?php while($s=$solicitudes->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['cliente']) ?></td>
                            <td><?= htmlspecialchars($s['propiedad']) ?></td>
                            <td><?= $s['fecha_solicitada'] ?></td>
                            <td>
                                <?php
                                $badgeClass = match($s['estado']) {
                                    'confirmada' => 'badge-disponible',
                                    'cancelada'  => 'badge-vendido',
                                    default      => 'badge-reservado'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($s['estado']) ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ultimos clientes -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Últimos clientes registrados</span>
                    <a href="clientes_asistente.php" class="btn-primary">Ver todos</a>
                </div>
                <table>
                    <thead>
                        <tr><th>#</th><th>Nombre</th><th>Correo</th><th>Usuario</th></tr>
                    </thead>
                    <tbody>
                    <?php if($clientes->rowCount()==0): ?>
                        <tr><td colspan="4" style="text-align:center;color:#6c757d;padding:20px">No hay clientes registrados.</td></tr>
                    <?php else: ?>
                        <?php while($c=$clientes->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                            <td><?= htmlspecialchars($c['correo']) ?></td>
                            <td><?= htmlspecialchars($c['usuario']) ?></td>
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