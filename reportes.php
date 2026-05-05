<?php
// Inicia sesión si no está activa
if (session_status() === PHP_SESSION_NONE) session_start();
// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión.");window.location="index.php";</script>'; exit();
}
// Verifica permisos: solo admin o asistente
if (!in_array($_SESSION['rol'], ['administrador','asistente'])) {
    echo '<script>alert("Sin permiso.");window.location="index.php";</script>'; exit();
}

// Incluye conexión a BD
include 'php/conexion_be.php';
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// Filtros para la consulta
$filtro_accion = $_GET['accion'] ?? 'todas';
$filtro_fecha  = $_GET['fecha']  ?? '';
$filtro_rol    = $_GET['rol']    ?? 'todos';

// Construir WHERE dinámico de forma segura
$condiciones = [];
$params      = [];
$tipos       = '';

if ($filtro_accion !== 'todas' && $filtro_accion !== '') {
    $condiciones[] = "accion = ?";
    $params[]      = $filtro_accion;
    $tipos        .= 's';
}
if ($filtro_rol !== 'todos' && $filtro_rol !== '') {
    $condiciones[] = "rol = ?";
    $params[]      = $filtro_rol;
    $tipos        .= 's';
}
if ($filtro_fecha !== '') {
    $condiciones[] = "DATE(fecha_hora) = ?";
    $params[]      = $filtro_fecha;
    $tipos        .= 's';
}

$where = count($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
$sql   = "SELECT * FROM registro_actividad $where ORDER BY fecha_hora DESC LIMIT 200";

if (count($params) > 0) {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $actividad = $stmt;
} else {
    $actividad = $conexion->query($sql);
}

// Totales para las tarjetas
$total_logins   = $conexion->query("SELECT COUNT(*) AS n FROM registro_actividad WHERE accion='Inicio de sesión'")->fetch(PDO::FETCH_ASSOC)['n'];
$total_fallidos = $conexion->query("SELECT COUNT(*) AS n FROM registro_actividad WHERE accion='Intento de sesión fallido'")->fetch(PDO::FETCH_ASSOC)['n'];
$total_props    = $conexion->query("SELECT COUNT(*) AS n FROM registro_actividad WHERE accion='Propiedad registrada'")->fetch(PDO::FETCH_ASSOC)['n'];
$total_hoy      = $conexion->query("SELECT COUNT(*) AS n FROM registro_actividad WHERE DATE(fecha_hora)=CURDATE()")->fetch(PDO::FETCH_ASSOC)['n'];

// acciones unicas para el filtro
$acciones_raw = $conexion->query("SELECT DISTINCT accion FROM registro_actividad ORDER BY accion");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
    <style>
        .filters-bar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px}
        .filter-group{display:flex;flex-direction:column;gap:4px}
        .filter-group label{font-size:11px;color:#6c757d;font-weight:500}
        .filter-group select,
        .filter-group input{padding:7px 10px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa}
        .filter-group select:focus,
        .filter-group input:focus{border-color:#46A2FD;background:#fff}
        .badge-accion{display:inline-block;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:500;white-space:nowrap}
        .accion-login    {background:#e8f5e9;color:#2e7d32}
        .accion-logout   {background:#fff8e1;color:#e65100}
        .accion-fallido  {background:#ffebee;color:#c62828}
        .accion-propiedad{background:#e3f2fd;color:#1565c0}
        .accion-visita   {background:#f3e5f5;color:#6a1b9a}
        .accion-usuario  {background:#e0f7fa;color:#00695c}
        .accion-default  {background:#f5f5f5;color:#424242}
        .rol-tag{font-size:11px;padding:2px 7px;border-radius:8px}
        .rol-administrador{background:#E6F1FB;color:#185FA5}
        .rol-agente       {background:#e8f5e9;color:#2e7d32}
        .rol-asistente    {background:#EEEDFE;color:#534AB7}
        .rol-cliente      {background:#fff8e1;color:#e65100}
        td.ip-cell{font-size:11px;color:#9e9e9e;font-family:monospace}
        .btn-export{background:transparent;border:1px solid #dee2e6;color:#6c757d;padding:7px 14px;border-radius:6px;font-size:12px;cursor:pointer;font-family:inherit;transition:all 200ms}
        .btn-export:hover{border-color:#46A2FD;color:#185FA5}
    </style>
</head>
<body>
<div class="layout">

    <?php
    //según rol
    if ($_SESSION['rol'] === 'administrador') {
        include 'include/sidebar.php';
    } else {
        include 'include/sidebar_usuario.php';
    }
    ?>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Reportes de actividad</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge" style="background:#E6F1FB;color:#185FA5;font-size:11px;padding:2px 8px;border-radius:10px"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>

        <div class="content">

            <!-- stats -->
            <div class="stats" style="margin-bottom:20px">
                <div class="stat-card">
                    <p class="stat-label">Inicios de sesión</p>
                    <p class="stat-value"><?= $total_logins ?></p>
                    <span class="badge badge-green" style="margin-top:6px;display:inline-block">Total histórico</span>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Intentos fallidos</p>
                    <p class="stat-value" style="color:#c62828"><?= $total_fallidos ?></p>
                    <span class="badge badge-red" style="margin-top:6px;display:inline-block">Alertas de seguridad</span>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Propiedades registradas</p>
                    <p class="stat-value"><?= $total_props ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Actividad hoy</p>
                    <p class="stat-value"><?= $total_hoy ?></p>
                    <span class="badge badge-blue" style="margin-top:6px;display:inline-block"><?= date('d/m/Y') ?></span>
                </div>
            </div>

            <!-- filtros -->
            <div class="card" style="margin-bottom:20px;padding:16px 20px">
                <form method="GET" action="reportes.php">
                    <div class="filters-bar">
                        <div class="filter-group">
                            <label>Tipo de acción</label>
                            <select name="accion">
                                <option value="todas">Todas las acciones</option>
                                <?php
                                foreach($acciones_raw->fetchAll(PDO::FETCH_ASSOC) as $a):
                                    $sel = $filtro_accion === $a['accion'] ? 'selected' : '';
                                ?>
                                <option value="<?= htmlspecialchars($a['accion']) ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($a['accion']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Rol</label>
                            <select name="rol">
                                <option value="todos">Todos los roles</option>
                                <option value="administrador" <?= $filtro_rol=='administrador'?'selected':'' ?>>Administrador</option>
                                <option value="agente"        <?= $filtro_rol=='agente'?'selected':'' ?>>Agente</option>
                                <option value="asistente"     <?= $filtro_rol=='asistente'?'selected':'' ?>>Asistente</option>
                                <option value="cliente"       <?= $filtro_rol=='cliente'?'selected':'' ?>>Cliente</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Fecha exacta</label>
                            <input type="date" name="fecha" value="<?= htmlspecialchars($filtro_fecha) ?>">
                        </div>
                        <div class="filter-group" style="justify-content:flex-end">
                            <label>&nbsp;</label>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn-primary">Filtrar</button>
                                <a href="reportes.php" class="btn-export">Limpiar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- tabla de actividad -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        Registro de actividad
                        <span style="font-size:12px;color:#6c757d;font-weight:400;margin-left:6px">
                            (mostrando últimos <?= $actividad->rowCount() ?> registros)
                        </span>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha y hora</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($actividad->rowCount()==0): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:#6c757d;padding:30px">
                                No hay registros con esos filtros.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while($r=$actividad->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php
                        //clase del badge según acción
                        $accionLower = strtolower($r['accion']);
                        if (str_contains($accionLower, 'inicio'))       $badgeAcc = 'accion-login';
                        elseif (str_contains($accionLower, 'cierre'))   $badgeAcc = 'accion-logout';
                        elseif (str_contains($accionLower, 'fallido'))  $badgeAcc = 'accion-fallido';
                        elseif (str_contains($accionLower, 'propiedad'))$badgeAcc = 'accion-propiedad';
                        elseif (str_contains($accionLower, 'visita'))   $badgeAcc = 'accion-visita';
                        elseif (str_contains($accionLower, 'usuario'))  $badgeAcc = 'accion-usuario';
                        else                                             $badgeAcc = 'accion-default';
                        ?>
                        <tr>
                            <td style="color:#9e9e9e;font-size:12px"><?= $r['id'] ?></td>
                            <td style="white-space:nowrap;font-size:12px">
                                <strong><?= date('d/m/Y', strtotime($r['fecha_hora'])) ?></strong><br>
                                <span style="color:#6c757d"><?= date('H:i:s', strtotime($r['fecha_hora'])) ?></span>
                            </td>
                            <td>
                                <div style="font-size:13px;font-weight:500"><?= htmlspecialchars($r['nombre'] ?? '—') ?></div>
                                <div style="font-size:11px;color:#9e9e9e"><?= htmlspecialchars($r['correo'] ?? '') ?></div>
                            </td>
                            <td>
                                <?php if($r['rol']): ?>
                                <span class="rol-tag rol-<?= $r['rol'] ?>"><?= ucfirst($r['rol']) ?></span>
                                <?php else: ?>
                                <span style="color:#9e9e9e;font-size:12px">—</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge-accion <?= $badgeAcc ?>"><?= htmlspecialchars($r['accion']) ?></span></td>
                            <td style="font-size:12px;color:#555;max-width:280px"><?= htmlspecialchars($r['descripcion'] ?? '') ?></td>
                            <td class="ip-cell"><?= htmlspecialchars($r['ip'] ?? '—') ?></td>
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