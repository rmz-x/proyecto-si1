<?php
require_once 'include/auth_admin.php';
include 'php/conexion_be.php';

// filtros
$usuario_filtro = $_GET['usuario'] ?? 'todos';
$fecha_desde    = $_GET['desde'] ?? date('Y-m-d', strtotime('-7 days'));
$fecha_hasta    = $_GET['hasta'] ?? date('Y-m-d');

// obtener lista de usuarios para el filtro
$usuarios_lista = $conexion->query("SELECT id, nombre FROM usuarios ORDER BY nombre");

// construir query con filtros
$where = "ra.accion IN ('Inicio de sesión', 'Cierre de sesión')";
$params = [];

if ($usuario_filtro !== 'todos') {
    $where .= " AND ra.usuario_id = ?";
    $params[] = $usuario_filtro;
}

$where .= " AND DATE(ra.fecha_hora) >= ? AND DATE(ra.fecha_hora) <= ?";
$params[] = $fecha_desde;
$params[] = $fecha_hasta;

$query = "SELECT u.nombre, u.correo, ra.accion, ra.fecha_hora, ra.ip
          FROM registro_actividad ra
          JOIN usuarios u ON ra.usuario_id = u.id
          WHERE $where
          ORDER BY ra.fecha_hora DESC
          LIMIT 500";

$stmt = $conexion->prepare($query);
$stmt->execute($params);
$actividades = $stmt;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad de Usuarios — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
    <style>
        .filters-bar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px}
        .filter-group{display:flex;flex-direction:column;gap:4px}
        .filter-group label{font-size:13px;color:#6c757d;font-weight:500}
        .filter-group select,.filter-group input{padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:14px}
        .badge-sesion{background:#e8f5e9;color:#2e7d32}
        .badge-cierre{background:#fff3e0;color:#e65100}
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Actividad de Usuarios</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
            </div>
        </div>
        <div class="content">
            <!-- filtros -->
            <div class="card" style="margin-bottom:20px;padding:16px 20px">
                <form method="GET" action="actividad_usuarios.php">
                    <div class="filters-bar">
                        <div class="filter-group">
                            <label>Usuario</label>
                            <select name="usuario">
                                <option value="todos">Todos los usuarios</option>
                                <?php foreach($usuarios_lista as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $usuario_filtro == $u['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Desde</label>
                            <input type="date" name="desde" value="<?= $fecha_desde ?>">
                        </div>
                        <div class="filter-group">
                            <label>Hasta</label>
                            <input type="date" name="hasta" value="<?= $fecha_hasta ?>">
                        </div>
                        <div class="filter-group">
                            <button type="submit" class="btn-primary" style="padding:8px 16px;margin-top:20px">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        Registros de sesiones
                        <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">(<?= $actividades->rowCount() ?> registros)</span>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Acción</th>
                            <th>Fecha y Hora</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($actividades->rowCount() == 0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No hay registros con esos filtros.</td></tr>
                    <?php else: ?>
                        <?php while($a = $actividades->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['nombre']) ?></td>
                            <td style="font-size:12px;color:#6c757d"><?= htmlspecialchars($a['correo']) ?></td>
                            <td>
                                <span class="badge <?= $a['accion'] == 'Inicio de sesión' ? 'badge-sesion' : 'badge-cierre' ?>">
                                    <?= $a['accion'] ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i:s', strtotime($a['fecha_hora'])) ?></td>
                            <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($a['ip']) ?></td>
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