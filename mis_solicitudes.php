<?php
// Verifica que el usuario esté logueado
require_once 'include/auth_check.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Obtiene ID del cliente y nombre
$cliente_id    = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// Consulta solicitudes de visita del cliente
$solicitudes = $conexion->query(
    "SELECT sv.*, p.titulo AS propiedad, p.zona, p.tipo
     FROM solicitudes_visita sv
     JOIN propiedades p ON sv.propiedad_id = p.id
     WHERE sv.cliente_id = $cliente_id
     ORDER BY sv.fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis solicitudes — Lorent</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
    <style>
        table{width:100%;border-collapse:collapse;font-size:13px}
        th{text-align:left;color:#6c757d;font-weight:500;padding:10px 14px;border-bottom:2px solid #e2e6ea;background:#f8f9fa;white-space:nowrap}
        td{padding:11px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle}
        tr:last-child td{border-bottom:none}
        tr:hover td{background:#f8f9fa}
        .badge{display:inline-block;font-size:11px;font-weight:500;padding:3px 10px;border-radius:10px}
        .badge-disponible{background:#e8f5e9;color:#2e7d32}
        .badge-reservado {background:#fff8e1;color:#e65100}
        .badge-vendido   {background:#ffebee;color:#c62828}
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Mis solicitudes de visita</span>
            <div class="user-area">
                <div class="avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge-role"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Historial de solicitudes</span>
                    <a href="ver_propiedades.php" class="btn-primary">+ Nueva solicitud</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Propiedad</th>
                            <th>Zona</th>
                            <th>Tipo</th>
                            <th>Fecha solicitada</th>
                            <th>Mensaje</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($solicitudes->rowCount()==0): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:#6c757d;padding:30px">
                                No tienes solicitudes aún.
                                <a href="ver_propiedades.php" style="color:#185FA5;margin-left:6px">Ver propiedades →</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while($s=$solicitudes->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['propiedad']) ?></td>
                            <td><?= htmlspecialchars($s['zona']) ?></td>
                            <td><?= $s['tipo'] ?></td>
                            <td><?= date('d/m/Y', strtotime($s['fecha_solicitada'])) ?></td>
                            <td style="max-width:200px;color:#6c757d">
                                <?= htmlspecialchars(substr($s['mensaje'],0,60)) ?>...
                            </td>
                            <td>
                                <?php
                                $cls = match($s['estado']) {
                                    'confirmada' => 'badge-disponible',
                                    'cancelada'  => 'badge-vendido',
                                    default      => 'badge-reservado'
                                };
                                ?>
                                <span class="badge <?= $cls ?>"><?= ucfirst($s['estado']) ?></span>
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