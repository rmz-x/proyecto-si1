<?php
require_once 'include/auth_admin.php';
include 'php/conexion_be.php';

$total_props  = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades"))['n'];
$disp_props   = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible'"))['n'];
$total_users  = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM usuarios"))['n'];
$total_ventas = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades WHERE estado='Vendido'"))['n'];

$ultimas = mysqli_query($conexion,
    "SELECT p.*, u.nombre AS agente
     FROM propiedades p
     LEFT JOIN usuarios u ON p.agente_id = u.id
     ORDER BY p.fecha_registro DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">

    <?php include 'include/sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Dashboard</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
            </div>
        </div>

        <!-- contenido -->
        <div class="content">

            <!-- estadisticas -->
            <div class="stats">
                <div class="stat-card">
                    <p class="stat-label">Total propiedades</p>
                    <p class="stat-value"><?= $total_props ?></p>
                    <span class="badge badge-green" style="margin-top:6px;display:inline-block"><?= $disp_props ?> disponibles</span>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Propiedades vendidas</p>
                    <p class="stat-value"><?= $total_ventas ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Usuarios del sistema</p>
                    <p class="stat-value"><?= $total_users ?></p>
                </div>
            </div>

            <!-- tabla ultimas propiedades -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Últimas propiedades registradas</span>
                    <a href="propiedades.php" class="btn-primary">Ver todas</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Zona</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Agente</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($ultimas) == 0): ?>
                        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay propiedades aún.</td></tr>
                    <?php else: ?>
                        <?php while($p = mysqli_fetch_assoc($ultimas)): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= htmlspecialchars($p['zona']) ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td>$<?= number_format($p['precio'], 0, ',', '.') ?></td>
                            <td><span class="badge badge-<?= strtolower($p['estado']) ?>"><?= $p['estado'] ?></span></td>
                            <td><?= htmlspecialchars($p['agente'] ?? 'Sin asignar') ?></td>
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
