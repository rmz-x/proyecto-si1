<?php
// Verifica que el usuario esté logueado
require_once 'include/auth_check.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Consulta datos del usuario actual
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$mensaje  = '';
$tipo_msg = '';

// Procesar formulario de actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']);
    $usuario   = trim($_POST['usuario']);
    $pass_nueva = $_POST['contrasena_nueva'];

    // Verificar si el usuario ya existe
    $ck = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id != ?");
    $ck->execute([$usuario, $_SESSION['user_id']]);

    if ($ck->rowCount() > 0) {
        $mensaje  = 'Ese nombre de usuario ya está en uso.';
        $tipo_msg = 'error';
    } else {
        if ($pass_nueva !== '') {
            $stmt2 = $conexion->prepare("UPDATE usuarios SET nombre=?, usuario=?, contrasena=? WHERE id=?");
            $stmt2->execute([$nombre, $usuario, $pass_nueva, $_SESSION['user_id']]);
        } else {
            $stmt2 = $conexion->prepare("UPDATE usuarios SET nombre=?, usuario=? WHERE id=?");
            $stmt2->execute([$nombre, $usuario, $_SESSION['user_id']]);
        }
        $_SESSION['nombre'] = $nombre;
        $mensaje  = 'Perfil actualizado correctamente.';
        $tipo_msg = 'ok';

        $stmt3 = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt3->execute([$_SESSION['user_id']]);
        $user = $stmt3->fetch(PDO::FETCH_ASSOC);
    }
}

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
    <style>
        .perfil-avatar{width:72px;height:72px;border-radius:50%;background:#1565c0;color:#B5D4F4;font-size:26px;font-weight:700;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
        .perfil-nombre{font-size:18px;font-weight:600;color:#0f4c75}
        .perfil-rol{font-size:13px;color:#6c757d;margin-top:3px}
        .msg-ok{background:#e8f5e9;border:1px solid #4caf50;color:#2e7d32;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px}
        .msg-error{background:#ffebee;border:1px solid #ef9a9a;color:#c62828;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px}
        .form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
        .form-group label{font-size:12px;color:#6c757d;font-weight:500}
        .form-group input{padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa;transition:border-color 200ms}
        .form-group input:focus{border-color:#64b5f6;background:#fff}
        .form-group input[readonly]{background:#f0f2f5;color:#6c757d;cursor:not-allowed}
        .divider{border:none;border-top:1px solid #e2e6ea;margin:20px 0}
        .section-label{font-size:13px;font-weight:600;color:#0f4c75;margin-bottom:14px}
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Mi perfil</span>
            <div class="user-area">
                <div class="avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge-role"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>

        <div class="content">
            <div class="card" style="max-width:520px">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
                    <div class="perfil-avatar"><?= strtoupper(substr($user['nombre'],0,2)) ?></div>
                    <div>
                        <p class="perfil-nombre"><?= htmlspecialchars($user['nombre']) ?></p>
                        <p class="perfil-rol"><?= ucfirst($user['rol']) ?> — <?= htmlspecialchars($user['correo']) ?></p>
                    </div>
                </div>

                <?php if($mensaje): ?>
                <div class="msg-<?= $tipo_msg==='ok'?'ok':'error' ?>"><?= $mensaje ?></div>
                <?php endif; ?>

                <form method="POST" action="mi_perfil.php">
                    <p class="section-label">Datos personales</p>

                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Correo electrónico (no se puede cambiar)</label>
                        <input type="email" value="<?= htmlspecialchars($user['correo']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nombre de usuario</label>
                        <input type="text" name="usuario" value="<?= htmlspecialchars($user['usuario']) ?>" required>
                    </div>

                    <hr class="divider">
                    <p class="section-label">Cambiar contraseña</p>
                    <div class="form-group">
                        <label>Nueva contraseña <span style="font-weight:400;color:#6c757d">(dejar vacío para no cambiar)</span></label>
                        <input type="password" name="contrasena_nueva" placeholder="Nueva contraseña">
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
                        <a href="javascript:history.back()" class="btn-detalle" style="padding:8px 18px">Cancelar</a>
                        <button type="submit" class="btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>