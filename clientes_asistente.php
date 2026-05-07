<?php
// Verifica que sea asistente o admin
require_once 'include/auth_asistente.php';
include 'php/conexion_be.php';

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

$clientes = $conexion->query(
    "SELECT id, nombre, correo, usuario, fecha_registro
     FROM usuarios
     WHERE rol='cliente'
     ORDER BY fecha_registro DESC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes — Asistente</title>
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
<?php include 'include/sidebar_usuario.php'; ?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Clientes registrados</span>
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
            <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
            <span class="badge">Asistente</span>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    Clientes del sistema (<?= $clientes->rowCount() ?>)
                </span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($clientes->rowCount()==0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:#777;padding:20px">
                            No hay clientes registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php while($c=$clientes->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['correo']) ?></td>
                        <td><?= htmlspecialchars($c['usuario']) ?></td>
                        <td><?= $c['fecha_registro'] ?></td>
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