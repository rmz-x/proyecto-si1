<?php
// Verifica que sea agente o admin
require_once 'include/auth_agente.php';
// Incluye conexión a BD
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis propiedades — Lorent</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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
                    <button class="btn-primary" onclick="abrirModal()">+ Registrar propiedad</button>
                </div>
                <table>
                    <thead>
                        <tr><th>#</th><th>Título</th><th>Tipo</th><th>Zona</th><th>Precio</th><th>Área</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if($props->rowCount()==0): ?>
                        <tr><td colspan="8" style="text-align:center;color:#6c757d;padding:20px">No tienes propiedades registradas.</td></tr>
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

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <h2 id="modalTitulo">Registrar propiedad</h2>
        <form action="php/registrar_propiedad_be.php" method="POST" id="formPropiedad">
            <input type="hidden" name="id"        id="propId"     value="">
            <input type="hidden" name="accion"    id="propAccion" value="registrar">
            <input type="hidden" name="agente_id" value="<?= $agente_id ?>">
            <div class="form-grid">
                <div class="form-group full">
                    <label>Título</label>
                    <input type="text" name="titulo" id="propTitulo" required>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo" id="propTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Zona</label>
                    <input type="text" name="zona" id="propZona" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" id="propPrecio" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Área (m²)</label>
                    <input type="number" name="area" id="propArea" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" id="propEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea name="descripcion" id="propDescripcion" rows="3" required></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-primary" id="btnSubmit">Registrar</button>
            </div>
        </form>
    </div>
</div>

<script src="asscet/js/validaciones.js"></script>
<script>
const overlay = document.getElementById('modalOverlay');
function abrirModal(){
    document.getElementById('formPropiedad').reset();
    document.getElementById('propId').value='';
    document.getElementById('propAccion').value='registrar';
    document.getElementById('modalTitulo').textContent='Registrar propiedad';
    document.getElementById('btnSubmit').textContent='Registrar';
    overlay.classList.add('open');
}
function cerrarModal(){ overlay.classList.remove('open'); }
overlay.addEventListener('click',e=>{ if(e.target===overlay) cerrarModal(); });
function editarPropiedad(id,titulo,tipo,zona,precio,area,descripcion,estado){
    document.getElementById('propId').value=id;
    document.getElementById('propAccion').value='modificar';
    document.getElementById('propTitulo').value=titulo;
    document.getElementById('propTipo').value=tipo;
    document.getElementById('propZona').value=zona;
    document.getElementById('propPrecio').value=precio;
    document.getElementById('propArea').value=area;
    document.getElementById('propDescripcion').value=descripcion;
    document.getElementById('propEstado').value=estado;
    document.getElementById('modalTitulo').textContent='Editar propiedad';
    document.getElementById('btnSubmit').textContent='Guardar cambios';
    overlay.classList.add('open');
}
function eliminar(id,titulo){
    if(confirm('¿Eliminar "'+titulo+'"?'))
        window.location='php/eliminar_propiedad_be.php?id='+id;
}
</script>
</body>
</html>