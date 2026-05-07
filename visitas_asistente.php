<?php
// Verifica asistente o admin
require_once 'include/auth_asistente.php';
include 'php/conexion_be.php';

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// filtros
$estados_validos = ['pendiente','confirmada','cancelada'];
$filtro = (isset($_GET['estado']) && in_array($_GET['estado'], $estados_validos))
        ? $_GET['estado']
        : 'todas';

$where = $filtro !== 'todas' ? "WHERE sv.estado = '$filtro'" : '';

$visitas = $conexion->query(
    "SELECT sv.*, 
            p.titulo AS propiedad, 
            p.zona,
            u.nombre AS cliente,
            u.correo AS correo_cliente,
            a.nombre AS agente
     FROM solicitudes_visita sv
     JOIN propiedades p ON sv.propiedad_id = p.id
     JOIN usuarios u    ON sv.cliente_id   = u.id
     JOIN usuarios a    ON p.agente_id     = a.id
     $where
     ORDER BY sv.fecha_solicitada ASC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Visitas — Asistente</title>
<link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
<?php include 'include/sidebar_usuario.php'; ?>

<div class="main">
<div class="topbar">
    <span class="topbar-title">Solicitudes de visita (global)</span>
    <div class="user-info">
        <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
        <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
        <span class="badge">Asistente</span>
    </div>
</div>

<div class="content">

<!-- filtros -->
<div class="card" style="margin-bottom:16px;padding:12px 18px">
    <a href="visitas_asistente.php" class="filter-tag <?= $filtro=='todas'?'active':'' ?>">Todas</a>
    <a href="?estado=pendiente" class="filter-tag <?= $filtro=='pendiente'?'active':'' ?>">Pendientes</a>
    <a href="?estado=confirmada" class="filter-tag <?= $filtro=='confirmada'?'active':'' ?>">Confirmadas</a>
    <a href="?estado=cancelada" class="filter-tag <?= $filtro=='cancelada'?'active':'' ?>">Canceladas</a>
</div>

<div class="card">
<div class="card-header">
    <span class="card-title">
        Visitas (<?= $visitas->rowCount() ?>)
    </span>
</div>

<table>
<thead>
<tr>
    <th>Propiedad</th>
    <th>Cliente</th>
    <th>Agente</th>
    <th>Fecha</th>
    <th>Estado</th>
</tr>
</thead>
<tbody>
<?php if($visitas->rowCount()==0): ?>
<tr><td colspan="5" style="text-align:center;padding:20px">No hay visitas.</td></tr>
<?php else: ?>
<?php while($v=$visitas->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
    <td><?= htmlspecialchars($v['propiedad']) ?></td>
    <td><?= htmlspecialchars($v['cliente']) ?></td>
    <td><?= htmlspecialchars($v['agente']) ?></td>
    <td><?= $v['fecha_solicitada'] ?></td>
    <td>
        <span class="badge"><?= ucfirst($v['estado']) ?></span>
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