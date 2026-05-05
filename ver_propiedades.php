<?php
// Verifica que el usuario esté logueado
require_once 'include/auth_check.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Validar filtro de tipo de propiedad
$tipos_validos = ['Venta', 'Alquiler', 'Anticretico'];
$filtro = (isset($_GET['tipo']) && in_array($_GET['tipo'], $tipos_validos)) ? $_GET['tipo'] : 'Todas';

if ($filtro !== 'Todas') {
    $stmt = $conexion->prepare(
        "SELECT * FROM propiedades WHERE estado='Disponible' AND tipo=? ORDER BY fecha_registro DESC"
    );
    $stmt->execute([$filtro]);
    $props = $stmt;
} else {
    $props = $conexion->query(
        "SELECT * FROM propiedades WHERE estado='Disponible' ORDER BY fecha_registro DESC");
}

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];
$current = basename($_SERVER['PHP_SELF']);

// Mensaje de éxito al solicitar visita
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propiedades — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
    <style>
        .filter-tag{display:inline-block;font-size:12px;padding:5px 14px;border:1px solid #dee2e6;border-radius:20px;color:#6c757d;cursor:pointer;transition:all 200ms}
        .filter-tag:hover{border-color:#64b5f6;color:#185FA5}
        .filter-tag.active{background:#E6F1FB;color:#185FA5;border-color:#85B7EB}
        .msg-ok{background:#e8f5e9;border:1px solid #4caf50;color:#2e7d32;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px}
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Propiedades disponibles</span>
            <div class="user-area">
                <div class="avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge-role"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>
        <div class="content">

            <?php if($msg === 'visita_ok'): ?>
            <div class="msg-ok">✓ Tu solicitud de visita fue enviada correctamente. Un agente se pondrá en contacto contigo.</div>
            <?php endif; ?>

            <!-- filtros -->
            <div class="card" style="margin-bottom:16px;padding:12px 18px">
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    <span style="font-size:13px;color:#6c757d;margin-right:4px">Filtrar:</span>
                    <a href="ver_propiedades.php"                    class="filter-tag <?= $filtro=='Todas'?'active':'' ?>">Todas</a>
                    <a href="ver_propiedades.php?tipo=Venta"         class="filter-tag <?= $filtro=='Venta'?'active':'' ?>">Venta</a>
                    <a href="ver_propiedades.php?tipo=Alquiler"      class="filter-tag <?= $filtro=='Alquiler'?'active':'' ?>">Alquiler</a>
                    <a href="ver_propiedades.php?tipo=Anticretico"   class="filter-tag <?= $filtro=='Anticretico'?'active':'' ?>">Anticréticos</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <?= $filtro==='Todas' ? 'Todas las propiedades' : "Propiedades: $filtro" ?>
                        <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:8px">(<?= $props->rowCount() ?> encontradas)</span>
                    </span>
                </div>
                <div class="prop-grid">
                <?php if($props->rowCount()==0): ?>
                    <p style="color:#6c757d;padding:20px 0;grid-column:1/-1;text-align:center">No hay propiedades disponibles.</p>
                <?php else: ?>
                    <?php while($p=$props->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="prop-card">
                        <div class="prop-img prop-img-<?= strtolower($p['tipo']) ?>">
                            <span class="prop-img-placeholder">Sin foto</span>
                            <span class="prop-tag tag-<?= strtolower($p['tipo']) ?>"><?= $p['tipo'] ?></span>
                        </div>
                        <div class="prop-body">
                            <p class="prop-title"><?= htmlspecialchars($p['titulo']) ?></p>
                            <p class="prop-zona"><?= htmlspecialchars($p['zona']) ?></p>
                            <div class="prop-footer">
                                <div>
                                    <p class="prop-price">$<?= number_format($p['precio'],0,',','.') ?></p>
                                    <p class="prop-area"><?= $p['area'] ? $p['area'].' m²' : '—' ?></p>
                                </div>
                                <a href="detalle_propiedad.php?id=<?= $p['id'] ?>" class="btn-detalle">Ver detalle</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>