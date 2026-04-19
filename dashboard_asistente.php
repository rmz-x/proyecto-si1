<?php
require_once 'include/auth_asistente.php';
include 'php/conexion_be.php';

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// estadisticas generales
$total_clientes  = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM usuarios WHERE rol='cliente'"))['n'];
$visitas_pend    = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM solicitudes_visita WHERE estado='pendiente'"))['n'];
$visitas_hoy     = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM solicitudes_visita WHERE fecha_solicitada=CURDATE()"))['n'];
$total_props     = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible'"))['n'];

// ultimas solicitudes de visita
$solicitudes = mysqli_query($conexion,
    "SELECT sv.*, p.titulo AS propiedad, u.nombre AS cliente
     FROM solicitudes_visita sv
     JOIN propiedades p ON sv.propiedad_id = p.id
     JOIN usuarios u    ON sv.cliente_id   = u.id
     ORDER BY sv.fecha_registro DESC LIMIT 8");

// ultimos clientes registrados
$clientes = mysqli_query($conexion,
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
                    <?php if(mysqli_num_rows($solicitudes)==0): ?>
                        <tr><td colspan="4" style="text-align:center;color:#6c757d;padding:20px">No hay solicitudes registradas.</td></tr>
                    <?php else: ?>
                        <?php while($s=mysqli_fetch_assoc($solicitudes)): ?>
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
                    <?php if(mysqli_num_rows($clientes)==0): ?>
                        <tr><td colspan="4" style="text-align:center;color:#6c757d;padding:20px">No hay clientes aún.</td></tr>
                    <?php else: ?>
                        <?php while($c=mysqli_fetch_assoc($clientes)): ?>
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