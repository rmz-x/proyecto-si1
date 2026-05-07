<?php
// Verifica que sea agente o admin
require_once 'include/auth_agente.php';
include 'php/conexion_be.php';

$agente_id     = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];

// Propiedades asignadas al agente
$props = $conexion->query(
    "SELECT * FROM propiedades WHERE agente_id = $agente_id ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis propiedades — Lorent</title>
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Mis propiedades</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($nombreUsuario) ?></span>
            </div>
        </div>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Lista de mis propiedades</span>
                    <!-- Botón solo visible para admin -->
                    <?php if($_SESSION['rol'] === 'admin'): ?>
                        <button class="btn-primary" onclick="abrirModal()">+ Registrar propiedad</button>
                    <?php endif; ?>
                </div>
                <table>
                    <thead>
                        <tr><th>#</th><th>Título</th><th>Tipo</th><th>Zona</th><th>Precio</th><th>Área</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if($props->rowCount()==0): ?>
                        <tr><td colspan="8" style="text-align:center;color:#6c757d;padding:20px">No tienes propiedades asignadas.</td></tr>
                    <?php else: ?>
                        <?php while($p=$props->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td><?= htmlspecialchars($p['zona']) ?></td>
                            <td>$<?= number_format($p['precio'],0,',','.') ?></td>
                            <td><?= $p['area'] ? $p['area'].' m²' : '—' ?></td>
                            <td><span class="badge badge-<?= strtolower($p['estado']) ?>"><?= $p['estado'] ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="editarPropiedad(
                                        <?= $p['id'] ?>,'<?= addslashes($p['titulo']) ?>',
                                        '<?= $p['tipo'] ?>','<?= addslashes($p['zona']) ?>',
                                        '<?= $p['precio'] ?>','<?= $p['area'] ?>',
                                        '<?= addslashes($p['descripcion']) ?>','<?= $p['estado'] ?>'
                                    )">Editar</button>
                                    <button class="btn-delete" onclick="eliminar(<?= $p['id'] ?>,'<?= addslashes($p['titulo']) ?>')">Eliminar</button>
                                </div>
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

<!-- MODAL solo para admin -->
<?php if($_SESSION['rol'] === 'admin'): ?>
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <h2 id="modalTitulo">Registrar propiedad</h2>
        <form action="php/registrar_propiedad_be.php" method="POST" id="formPropiedad">
            <input type="hidden" name="id"        id="propId"     value="">
            <input type="hidden" name="accion"    id="propAccion" value="registrar">
            <input type="hidden" name="agente_id" value="<?= $agente_id ?>">
            <!-- Aquí van los campos del formulario -->
            <!-- ... -->
        </form>
    </div>
</div>
<?php endif; ?>

<script src="asscet/js/validaciones.js"></script>
<!-- Scripts de modal y edición se mantienen -->
</body>
</html>
