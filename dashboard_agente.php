<?php
// Verifica que el usuario sea agente o admin antes de mostrar la página
require_once 'include/auth_agente.php';
// Incluye la conexión a la base de datos
include 'php/conexion_be.php';

// Obtiene el nombre del usuario de la sesión
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];
// Obtiene el ID del agente de la sesión
$agente_id     = $_SESSION['user_id'];

// Estadísticas del agente: cuenta propiedades asignadas
$mis_props = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE agente_id = $agente_id")->fetch(PDO::FETCH_ASSOC)['n'];
// Cuenta propiedades disponibles
$disponibles = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE agente_id = $agente_id AND estado='Disponible'")->fetch(PDO::FETCH_ASSOC)['n'];
// Cuenta propiedades vendidas
$vendidas = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE agente_id = $agente_id AND estado='Vendido'")->fetch(PDO::FETCH_ASSOC)['n'];
// Cuenta visitas pendientes
$visitas_pend = $conexion->query("SELECT COUNT(*) AS n FROM solicitudes_visita sv JOIN propiedades p ON sv.propiedad_id = p.id WHERE p.agente_id = $agente_id AND sv.estado='pendiente'")->fetch(PDO::FETCH_ASSOC)['n'];

// Consulta las últimas 5 propiedades del agente
$props = $conexion->query("SELECT * FROM propiedades WHERE agente_id = $agente_id ORDER BY fecha_registro DESC LIMIT 5");

// Consulta solicitudes de visita pendientes con detalles
$visitas = $conexion->query("SELECT sv.*, p.titulo AS propiedad, u.nombre AS cliente FROM solicitudes_visita sv JOIN propiedades p ON sv.propiedad_id = p.id JOIN usuarios u ON sv.cliente_id = u.id WHERE p.agente_id = $agente_id AND sv.estado = 'pendiente' ORDER BY sv.fecha_solicitada ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Agente — Lorent Inmobiliaria</title>
    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS del dashboard -->
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
    <!-- Incluye el menú lateral personalizado por rol -->
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Panel del Agente</span>
            <div class="user-info">
                <!-- Avatar con iniciales -->
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
                <!-- Badge indicando rol de agente -->
                <span class="badge" style="background:#E6F1FB;color:#185FA5;font-size:11px;padding:2px 8px;border-radius:10px">Agente</span>
            </div>
        </div>
        <div class="content">

            <!-- Sección de estadísticas -->
            <div class="stats">
                <div class="stat-card">
                    <p class="stat-label">Mis propiedades</p>
                    <p class="stat-value"><?= $mis_props ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Disponibles</p>
                    <p class="stat-value"><?= $disponibles ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Vendidas</p>
                    <p class="stat-value"><?= $vendidas ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Visitas pendientes</p>
                    <p class="stat-value"><?= $visitas_pend ?></p>
                </div>
            </div>

            <!-- Mis propiedades -->
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <span class="card-title">Mis propiedades recientes</span>
                    <a href="propiedades_agente.php" class="btn-primary">Ver todas</a>
                </div>
                <table>
                    <thead>
                        <tr><th>Título</th><th>Zona</th><th>Tipo</th><th>Precio</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                    <?php if($props->rowCount()==0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No tienes propiedades asignadas aún.</td></tr>
                    <?php else: ?>
                        <?php while($p=$props->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= htmlspecialchars($p['zona']) ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td>$<?= number_format($p['precio'],0,',','.') ?></td>
                            <td><span class="badge badge-<?= strtolower($p['estado']) ?>"><?= $p['estado'] ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Visitas pendientes -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Solicitudes de visita pendientes</span>
                    <a href="visitas_agente.php" class="btn-primary">Ver todas</a>
                </div>
                <table>
                    <thead>
                        <tr><th>Propiedad</th><th>Cliente</th><th>Fecha solicitada</th><th>Mensaje</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                    <?php if($visitas->rowCount()==0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No hay visitas pendientes.</td></tr>
                    <?php else: ?>
                        <?php while($v=$visitas->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['propiedad']) ?></td>
                            <td><?= htmlspecialchars($v['cliente']) ?></td>
                            <td><?= $v['fecha_solicitada'] ?></td>
                            <td><?= htmlspecialchars(substr($v['mensaje'],0,40)) ?>...</td>
                            <td>
                                <div class="action-btns">
                                    <a href="php/confirmar_visita_be.php?id=<?= $v['id'] ?>&estado=confirmada" class="btn-edit">Confirmar</a>
                                    <a href="php/confirmar_visita_be.php?id=<?= $v['id'] ?>&estado=cancelada"  class="btn-delete">Cancelar</a>
                                </div>
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