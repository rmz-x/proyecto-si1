<?php
// Verifica que sea admin
require_once 'include/auth_admin.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Obtener agentes para el select del formulario
$agentes = $conexion->query("SELECT id, nombre FROM usuarios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las propiedades
$props = $conexion->query(
    "SELECT p.*, u.nombre AS agente
     FROM propiedades p
     LEFT JOIN usuarios u ON p.agente_id = u.id
     ORDER BY p.fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propiedades — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard.css">
</head>
<body>
<div class="layout">

    <?php include 'include/sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Propiedades</span>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 2)) ?></div>
                <span class="user-email"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Lista de propiedades</span>
                    <button class="btn-primary" onclick="abrirModal()">+ Registrar propiedad</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Zona</th>
                            <th>Precio</th>
                            <th>Área m²</th>
                            <th>Estado</th>
                            <th>Agente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($props->rowCount() == 0): ?>
                        <tr><td colspan="9" style="text-align:center;color:#6c757d;padding:20px">No hay propiedades registradas.</td></tr>
                    <?php else: ?>
                        <?php while($p = $props->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td><?= htmlspecialchars($p['zona']) ?></td>
                            <td>$<?= number_format($p['precio'], 0, ',', '.') ?></td>
                            <td><?= $p['area'] ? $p['area'].' m²' : '—' ?></td>
                            <td><span class="badge badge-<?= strtolower($p['estado']) ?>"><?= $p['estado'] ?></span></td>
                            <td><?= htmlspecialchars($p['agente'] ?? 'Sin asignar') ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="editarPropiedad(
                                        <?= $p['id'] ?>,
                                        '<?= addslashes($p['titulo']) ?>',
                                        '<?= $p['tipo'] ?>',
                                        '<?= addslashes($p['zona']) ?>',
                                        '<?= $p['precio'] ?>',
                                        '<?= $p['area'] ?>',
                                        '<?= addslashes($p['descripcion']) ?>',
                                        '<?= $p['estado'] ?>',
                                        '<?= $p['agente_id'] ?>'
                                    )">Editar</button>
                                    <button class="btn-delete" onclick="eliminarPropiedad(<?= $p['id'] ?>, '<?= addslashes($p['titulo']) ?>')">Eliminar</button>
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
        <h2 id="modalTitulo">Registrar propiedad</h2>

        <form action="php/registrar_propiedad_be.php" method="POST" id="formPropiedad">
            <input type="hidden" name="id" id="propId" value="">
            <input type="hidden" name="accion" id="propAccion" value="registrar">

            <div class="form-grid">
                <div class="form-group full">
                    <label>Título de la propiedad</label>
                    <input type="text" name="titulo" id="propTitulo" placeholder="Ej: Casa Urubó — 350m²" required>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo" id="propTipo" required>
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Zona</label>
                    <input type="text" name="zona" id="propZona" placeholder="Ej: Urubó, Equipetrol" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" id="propPrecio" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Área (m²)</label>
                    <input type="number" name="area" id="propArea" placeholder="0" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" id="propEstado" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Agente</label>
                    <select name="agente_id" id="propAgente">
                        <option value="">Sin asignar</option>
                        <?php
                        foreach($agentes as $a):
                        ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea name="descripcion" id="propDescripcion" rows="3" placeholder="Detalles adicionales..."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-primary" id="btnSubmit">Registrar</button>
            </div>
        </form>
    </div>
</div>

<script>
const overlay = document.getElementById('modalOverlay');

function abrirModal() {
    //limpiar formulario para nuevo registro
    document.getElementById('formPropiedad').reset();
    document.getElementById('propId').value     = '';
    document.getElementById('propAccion').value = 'registrar';
    document.getElementById('modalTitulo').textContent = 'Registrar propiedad';
    document.getElementById('btnSubmit').textContent   = 'Registrar';
    overlay.classList.add('open');
}

function cerrarModal() {
    overlay.classList.remove('open');
}

//cerrar si se hace clic fuera del modal
overlay.addEventListener('click', function(e) {
    if (e.target === overlay) cerrarModal();
});

function editarPropiedad(id, titulo, tipo, zona, precio, area, descripcion, estado, agenteId) {
    document.getElementById('propId').value          = id;
    document.getElementById('propAccion').value      = 'modificar';
    document.getElementById('propTitulo').value      = titulo;
    document.getElementById('propTipo').value        = tipo;
    document.getElementById('propZona').value        = zona;
    document.getElementById('propPrecio').value      = precio;
    document.getElementById('propArea').value        = area;
    document.getElementById('propDescripcion').value = descripcion;
    document.getElementById('propEstado').value      = estado;
    document.getElementById('propAgente').value      = agenteId;
    document.getElementById('modalTitulo').textContent = 'Editar propiedad';
    document.getElementById('btnSubmit').textContent   = 'Guardar cambios';
    overlay.classList.add('open');
}

function eliminarPropiedad(id, titulo) {
    if (confirm('¿Eliminar la propiedad "' + titulo + '"?\nEsta acción no se puede deshacer.')) {
        window.location = 'php/eliminar_propiedad_be.php?id=' + id;
    }
}
</script>
<script src="asscet/js/validaciones.js"></script>
</body>
</html>
