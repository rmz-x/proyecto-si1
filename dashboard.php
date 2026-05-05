<?php
// Verifica que el usuario sea administrador antes de mostrar la página
require_once 'include/auth_admin.php';
// Incluye la conexión a la base de datos
include 'php/conexion_be.php';

// Consulta el total de propiedades en la base de datos
$total_props  = $conexion->query("SELECT COUNT(*) AS n FROM propiedades")->fetch(PDO::FETCH_ASSOC)['n'];
// Consulta las propiedades disponibles
$disp_props   = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible'")->fetch(PDO::FETCH_ASSOC)['n'];
// Consulta el total de usuarios registrados
$total_users  = $conexion->query("SELECT COUNT(*) AS n FROM usuarios")->fetch(PDO::FETCH_ASSOC)['n'];
// Consulta las propiedades vendidas
$total_ventas = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE estado='Vendido'")->fetch(PDO::FETCH_ASSOC)['n'];

// Consulta las últimas 5 propiedades registradas con información del agente
$ultimas = $conexion->query(
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
    <!-- Fuentes de Google para el estilo -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS principales -->
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">

    <!-- Incluye el menú lateral -->
    <?php include 'include/sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Dashboard</span>
            <div class="user-info">
                <!-- Avatar con las iniciales del usuario -->
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
            </div>
        </div>

        <!-- Contenido principal de la página -->
        <div class="content">

            <!-- Sección de estadísticas -->
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
                    <?php if($ultimas->rowCount() == 0): ?>
                        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay propiedades aún.</td></tr>
                    <?php else: ?>
                        <?php while($p = $ultimas->fetch(PDO::FETCH_ASSOC)): ?>
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
