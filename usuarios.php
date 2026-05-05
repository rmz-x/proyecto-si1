<?php
// Verifica que sea admin
require_once 'include/auth_admin.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Obtener todos los usuarios
$usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">

    <?php include 'include/sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Gestión de usuarios</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Lista de usuarios</span>
                    <button class="btn-primary" onclick="abrirModal()">+ Agregar usuario</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($usuarios->rowCount() == 0): ?>
                        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay usuarios registrados.</td></tr>
                    <?php else: ?>
                        <?php while($u = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td><?= htmlspecialchars($u['correo']) ?></td>
                            <td><?= htmlspecialchars($u['usuario']) ?></td>
                            <td>
                                <?php
                                $rolClase = match($u['rol'] ?? 'agente') {
                                    'administrador' => 'badge-blue',
                                    'asistente'     => 'badge-purple',
                                    default         => 'badge-green'
                                };
                                ?>
                                <span class="badge <?= $rolClase ?>"><?= $u['rol'] ?? 'agente' ?></span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="editarUsuario(
                                        <?= $u['id'] ?>,
                                        '<?= addslashes($u['nombre']) ?>',
                                        '<?= addslashes($u['correo']) ?>',
                                        '<?= addslashes($u['usuario']) ?>',
                                        '<?= $u['rol'] ?? 'agente' ?>'
                                    )">Editar</button>
                                    <button class="btn-delete" onclick="eliminarUsuario(<?= $u['id'] ?>, '<?= addslashes($u['nombre']) ?>')">Eliminar</button>
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

<!-- formulario -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <h2 id="modalTitulo">Agregar usuario</h2>

        <form action="php/gestionar_usuario_be.php" method="POST" id="formUsuario">
            <input type="hidden" name="id"     id="userId"     value="">
            <input type="hidden" name="accion" id="userAccion" value="agregar">

            <div class="form-grid">
                <div class="form-group full">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" id="userName" placeholder="Nombre y apellidos" required>
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" id="userCorreo" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="form-group">
                    <label>Nombre de usuario</label>
                    <input type="text" name="usuario" id="userUsuario" placeholder="usuario123" required>
                </div>
                <div class="form-group">
                    <label>Contraseña <span id="passNote" style="color:#6c757d;font-weight:400">(requerida)</span></label>
                    <div class="password-wrapper" style="position:relative">
                        <input type="password" name="contrasena" id="userPass" placeholder="Mínimo 6 caracteres">
                        <button class="password-toggle-btn" title="Mostrar contraseña">👁️</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="rol" id="userRol" required>
                        <option value="agente">Agente</option>
                        <option value="administrador">Administrador</option>
                        <option value="asistente">Asistente</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-primary" id="btnSubmit">Agregar</button>
            </div>
        </form>
    </div>
</div>

<script>
const overlay = document.getElementById('modalOverlay');

function abrirModal() {
    document.getElementById('formUsuario').reset();
    document.getElementById('userId').value     = '';
    document.getElementById('userAccion').value = 'agregar';
    document.getElementById('modalTitulo').textContent = 'Agregar usuario';
    document.getElementById('btnSubmit').textContent   = 'Agregar';
    document.getElementById('passNote').textContent    = '(requerida)';
    document.getElementById('userPass').required       = true;
    overlay.classList.add('open');
}

function cerrarModal() {
    overlay.classList.remove('open');
}

overlay.addEventListener('click', function(e) {
    if (e.target === overlay) cerrarModal();
});

function editarUsuario(id, nombre, correo, usuario, rol) {
    document.getElementById('userId').value     = id;
    document.getElementById('userAccion').value = 'editar';
    document.getElementById('userName').value   = nombre;
    document.getElementById('userCorreo').value = correo;
    document.getElementById('userUsuario').value= usuario;
    document.getElementById('userRol').value    = rol;
    document.getElementById('userPass').value   = '';
    document.getElementById('userPass').required= false;
    document.getElementById('passNote').textContent = '(dejar vacío para no cambiar)';
    document.getElementById('modalTitulo').textContent = 'Editar usuario';
    document.getElementById('btnSubmit').textContent   = 'Guardar cambios';
    overlay.classList.add('open');
}

function eliminarUsuario(id, nombre) {
    if (confirm('¿Eliminar al usuario "' + nombre + '"?\nEsta acción no se puede deshacer.')) {
        window.location = 'php/gestionar_usuario_be.php?accion=eliminar&id=' + id;
    }
}
</script>
<script src="asscet/js/validaciones.js"></script>
</body>
</html>
