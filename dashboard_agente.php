<?php
// Verifica que el usuario sea agente antes de mostrar la página
require_once 'include/auth_agente.php';
include 'php/conexion_be.php';

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];
$agente_id     = $_SESSION['user_id'];

// Estadísticas del agente
$mis_props     = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE agente_id = $agente_id")->fetch(PDO::FETCH_ASSOC)['n'];
$disponibles   = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE agente_id = $agente_id AND estado='Disponible'")->fetch(PDO::FETCH_ASSOC)['n'];
$vendidas      = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE agente_id = $agente_id AND estado='Vendido'")->fetch(PDO::FETCH_ASSOC)['n'];
$visitas_pend  = $conexion->query("SELECT COUNT(*) AS n 
                                   FROM solicitudes_visita sv 
                                   JOIN propiedades p ON sv.propiedad_id = p.id 
                                   WHERE p.agente_id = $agente_id AND sv.estado='pendiente'")
                                   ->fetch(PDO::FETCH_ASSOC)['n'];

// Últimas 5 propiedades
$props = $conexion->query("SELECT * FROM propiedades WHERE agente_id = $agente_id ORDER BY fecha_registro DESC LIMIT 5");

// Solicitudes de visita pendientes con tipo de propiedad
$visitas = $conexion->query("SELECT sv.id, sv.propiedad_id, sv.cliente_id,
                                sv.telefono, sv.fecha_solicitada, sv.mensaje,
                                p.titulo AS propiedad, p.tipo,
                                u.nombre AS cliente
                            FROM solicitudes_visita sv
                            JOIN propiedades p ON sv.propiedad_id = p.id
                            JOIN usuarios u ON sv.cliente_id = u.id
                            WHERE p.agente_id = $agente_id AND sv.estado = 'pendiente'
                            ORDER BY sv.fecha_solicitada ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Agente — Lorent Inmobiliaria</title>
    <link rel="stylesheet" href="asscet/css/dashboard.css">
    <style>
        .badge-venta { background:#E6F1FB; color:#185FA5; padding:2px 8px; border-radius:10px; font-size:11px; }
        .badge-alquiler { background:#E8F5E9; color:#2E7D32; padding:2px 8px; border-radius:10px; font-size:11px; }
        .badge-anticretico { background:#F3E5F5; color:#6A1B9A; padding:2px 8px; border-radius:10px; font-size:11px; }
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Panel del Agente</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge" style="background:#E6F1FB;color:#185FA5;font-size:11px;padding:2px 8px;border-radius:10px">Agente</span>
            </div>
        </div>
        <div class="content">

            <!-- Estadísticas -->
            <div class="stats">
                <div class="stat-card"><p class="stat-label">Mis propiedades</p><p class="stat-value"><?= $mis_props ?></p></div>
                <div class="stat-card"><p class="stat-label">Disponibles</p><p class="stat-value"><?= $disponibles ?></p></div>
                <div class="stat-card"><p class="stat-label">Vendidas</p><p class="stat-value"><?= $vendidas ?></p></div>
                <div class="stat-card"><p class="stat-label">Visitas pendientes</p><p class="stat-value"><?= $visitas_pend ?></p></div>
            </div>

            <!-- Mis propiedades -->
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <span class="card-title">Mis propiedades recientes</span>
                    <a href="propiedades_agente.php" class="btn-primary">Ver todas</a>
                </div>
                <table>
                    <thead><tr><th>Título</th><th>Zona</th><th>Tipo</th><th>Precio</th><th>Estado</th></tr></thead>
                    <tbody>
                    <?php if($props->rowCount()==0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No tienes propiedades asignadas aún.</td></tr>
                    <?php else: while($p=$props->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= htmlspecialchars($p['zona']) ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td>$<?= number_format($p['precio'],0,',','.') ?></td>
                            <td><span class="badge badge-<?= strtolower($p['estado']) ?>"><?= $p['estado'] ?></span></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Prospectos CRM -->
            <div class="card" style="margin-top:20px; text-align:center">
                <div class="card-header"><span class="card-title">Prospectos CRM</span></div>
                <div class="card-body"><a href="prospectos_crm.php" class="btn-primary">Gestionar Prospectos</a></div>
            </div>

            <!-- Visitas pendientes -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Solicitudes de visita pendientes</span>
                    <a href="visitas_agente.php" class="btn-primary">Ver todas</a>
                </div>
                <table>
                    <thead>
                        <tr> <th>Propiedad</th> <th>Tipo</th> <th>Cliente</th> <th>Número</th> <th>Fecha solicitada</th> <th>Mensaje</th>
                        <th style="text-align:center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($visitas->rowCount()==0): ?>
                        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay visitas pendientes.</td></tr>
                    <?php else: while($v=$visitas->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['propiedad']) ?></td>
                            <td><span class="badge badge-<?= strtolower($v['tipo']) ?>"><?= $v['tipo'] ?></span></td>
                            <td><?= htmlspecialchars($v['cliente']) ?></td>
                            <td><?= htmlspecialchars($v['telefono']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($v['fecha_solicitada'])) ?></td>
                            <td><?= htmlspecialchars(substr($v['mensaje'],0,40)) ?>...</td>
                            <td style="text-align:center">
                                <div class="action-btns">
                                <a href="php/confirmar_visita_be.php?id=<?= $v['id'] ?>&estado=confirmada" class="btn-edit">Confirmar</a>
                                <a href="php/confirmar_visita_be.php?id=<?= $v['id'] ?>&estado=cancelada" class="btn-delete">Cancelar</a>
                                <a href="registrar_cliente_interesado.php?propiedad_id=<?= $v['propiedad_id'] ?>&cliente_id=<?= $v['cliente_id'] ?>" class="btn-registrar">Registrar</a>
                                </div>
                            </td>
                        </tr>

                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Clientes interesados -->
            <div class="card" style="margin-top:20px; text-align:center">
                <div class="card-header"><span class="card-title">Clientes interesados</span></div>
                <div class="card-body"><a href="clientes_interesados.php" class="btn-primary">Ver clientes interesados</a></div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
