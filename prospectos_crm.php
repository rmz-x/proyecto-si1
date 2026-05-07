<?php
require_once 'include/auth_check.php';
include 'php/conexion_be.php';

// Solo agentes y administradores pueden acceder
if ($_SESSION['rol'] !== 'agente' && $_SESSION['rol'] !== 'administrador') {
    if ($_SESSION['rol'] === 'cliente') {
        header("Location: dashboard_usuario.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Filtro por tipo de propiedad
$filtro = $_GET['tipo'] ?? null;

$sql = "SELECT * FROM vista_prospectos_propiedades";
if ($filtro) {
    $sql .= " WHERE tipo_propiedad = :filtro";
}
$sql .= " ORDER BY fecha_interes DESC";

$stmt = $conexion->prepare($sql);
if ($filtro) {
    $stmt->execute([':filtro' => $filtro]);
} else {
    $stmt->execute();
}
$prospectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Prospectos CRM — Lorent</title>
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Prospectos CRM</span>
            <div class="user-area">
                <div class="avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge-role"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>
        <div class="content">
            <div class="card">
                <h3>Listado de prospectos</h3>

                <!-- Botones de filtro -->
                <div class="filters" style="margin-bottom:15px;">
                    <a href="prospectos_crm.php?tipo=Venta" class="btn-filter">Solo Venta</a>
                    <a href="prospectos_crm.php?tipo=Alquiler" class="btn-filter">Solo Alquiler</a>
                    <a href="prospectos_crm.php?tipo=Anticretico" class="btn-filter">Solo Anticrético</a>
                    <a href="prospectos_crm.php" class="btn-filter">Ver todos</a>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Prospecto</th>
                            <th>Propiedad</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($prospectos)): ?>
                            <tr><td colspan="6" class="empty">No hay prospectos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach($prospectos as $p): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($p['prospecto_nombre']) ?></strong><br>
                                    📧 <?= htmlspecialchars($p['correo'] ?? '') ?><br>
                                    📱 <?= htmlspecialchars($p['telefono'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars($p['propiedad_titulo']) ?></td>
                                <td><?= htmlspecialchars($p['tipo_propiedad']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $p['estado_prospecto'] ?>">
                                        <?= ucfirst($p['estado_prospecto']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($p['fecha_interes'])) ?></td>
                                <td>
                                <form method="POST" action="php/gestionar_prospecto_be.php" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $p['prospecto_id'] ?>">
                                    <select name="estado" class="estado-select">
                                        <option value="nuevo" <?= $p['estado_prospecto']=='nuevo'?'selected':'' ?>>Nuevo</option>
                                        <option value="contactado" <?= $p['estado_prospecto']=='contactado'?'selected':'' ?>>Contactado</option>
                                        <option value="negociacion" <?= $p['estado_prospecto']=='negociacion'?'selected':'' ?>>En negociación</option>
                                        <option value="cerrado" <?= $p['estado_prospecto']=='cerrado'?'selected':'' ?>>Cerrado</option>
                                        <option value="descartado" <?= $p['estado_prospecto']=='descartado'?'selected':'' ?>>Descartado</option>
                                    </select>
                                    <button type="submit" class="btn-primary">Actualizar</button>
                                </form>
                            </td>

                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
</body>
</html>
