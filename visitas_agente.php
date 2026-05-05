<?php
// Verifica que sea agente o admin
require_once 'include/auth_agente.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

$agente_id     = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// Filtro por estado
$estados_validos = ['pendiente','confirmada','cancelada'];
$filtro = (isset($_GET['estado']) && in_array($_GET['estado'], $estados_validos)) ? $_GET['estado'] : 'todas';

$where = $filtro !== 'todas' ? "AND sv.estado='$filtro'" : '';

$visitas = $conexion->query(
    "SELECT sv.*, p.titulo AS propiedad, p.zona, u.nombre AS cliente, u.correo AS correo_cliente
     FROM solicitudes_visita sv
     JOIN propiedades p ON sv.propiedad_id = p.id
     JOIN usuarios u    ON sv.cliente_id   = u.id
     WHERE p.agente_id = $agente_id $where
     ORDER BY sv.fecha_solicitada ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitas — Lorent</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
    <style>
        .filter-tag{display:inline-block;font-size:12px;padding:5px 14px;border:1px solid #dee2e6;border-radius:20px;color:#6c757d;margin-right:6px;transition:all 200ms}
        .filter-tag:hover{border-color:#46A2FD;color:#185FA5}
        .filter-tag.active{background:#E6F1FB;color:#185FA5;border-color:#85B7EB}
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Solicitudes de visita</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
            </div>
        </div>
        <div class="content">

            <!-- filtros -->
            <div class="card" style="margin-bottom:16px;padding:12px 18px">
                <div style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
                    <span style="font-size:13px;color:#6c757d;margin-right:6px">Filtrar:</span>
                    <a href="visitas_agente.php"                    class="filter-tag <?= $filtro=='todas'?'active':'' ?>">Todas</a>
                    <a href="visitas_agente.php?estado=pendiente"   class="filter-tag <?= $filtro=='pendiente'?'active':'' ?>">Pendientes</a>
                    <a href="visitas_agente.php?estado=confirmada"  class="filter-tag <?= $filtro=='confirmada'?'active':'' ?>">Confirmadas</a>
                    <a href="visitas_agente.php?estado=cancelada"   class="filter-tag <?= $filtro=='cancelada'?'active':'' ?>">Canceladas</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        Visitas
                        <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">(<?= $visitas->rowCount() ?> registros)</span>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr><th>Propiedad</th><th>Cliente</th><th>Correo</th><th>Fecha</th><th>Mensaje</th><th>Estado</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                    <?php if($visitas->rowCount()==0): ?>
                        <tr><td colspan="7" style="text-align:center;color:#6c757d;padding:20px">No hay solicitudes con ese filtro.</td></tr>
                    <?php else: ?>
                        <?php while($v=$visitas->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['propiedad']) ?></td>
                            <td><?= htmlspecialchars($v['cliente']) ?></td>
                            <td style="font-size:12px;color:#6c757d"><?= htmlspecialchars($v['correo_cliente']) ?></td>
                            <td><?= $v['fecha_solicitada'] ?></td>
                            <td style="font-size:12px;color:#6c757d;max-width:160px"><?= htmlspecialchars(substr($v['mensaje'],0,50)) ?>...</td>
                            <td>
                                <?php $cls = match($v['estado']){'confirmada'=>'badge-disponible','cancelada'=>'badge-vendido',default=>'badge-reservado'}; ?>
                                <span class="badge <?= $cls ?>"><?= ucfirst($v['estado']) ?></span>
                            </td>
                            <td>
                                <?php if($v['estado']==='pendiente'): ?>
                                <div class="action-btns">
                                    <a href="php/confirmar_visita_be.php?id=<?= $v['id'] ?>&estado=confirmada" class="btn-edit">Confirmar</a>
                                    <a href="php/confirmar_visita_be.php?id=<?= $v['id'] ?>&estado=cancelada"  class="btn-delete">Cancelar</a>
                                </div>
                                <?php else: ?>
                                <span style="font-size:12px;color:#6c757d">—</span>
                                <?php endif; ?>
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