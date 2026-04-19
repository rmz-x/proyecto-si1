<?php
require_once 'include/auth_check.php';
include 'php/conexion_be.php';
 
// solo usuarios con rol agente, asistente u otro que no sea administrador
if ($_SESSION['rol'] === 'administrador') {
    header("location: dashboard.php");
    exit();
}
 
// estadisticas visibles al usuario
$total_disp = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible'"))['n'];
$total_venta = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible' AND tipo='Venta'"))['n'];
$total_alquiler = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible' AND tipo='Alquiler'"))['n'];
 
// propiedades disponibles
$props = mysqli_query($conexion,
    "SELECT * FROM propiedades WHERE estado='Disponible' ORDER BY fecha_registro DESC");
 
// nombre del usuario
$infoUser = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT nombre FROM usuarios WHERE correo='" . $_SESSION['usuario'] . "'"));
$nombreUsuario = $infoUser['nombre'] ?? $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="logo">
            <span class="logo-title">Lorent</span>
            <span class="logo-sub">Inmobiliaria</span>
        </div>
        <nav class="nav">
            <p class="nav-section">Menú</p>
            <a href="dashboard_usuario.php" class="nav-item active">
                <span class="nav-dot" style="background:#64b5f6"></span>Inicio
            </a>
            <a href="ver_propiedades.php" class="nav-item">
                <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
            </a>
            <p class="nav-section">Cuenta</p>
            <a href="mi_perfil.php" class="nav-item">
                <span class="nav-dot" style="background:#AFA9EC"></span>Mi perfil
            </a>
        </nav>
        <a href="php/cerrar_sesion.php" class="nav-logout">↩ Cerrar sesión</a>
    </aside>
 
    <div class="main">

        <div class="topbar">
            <span class="topbar-title">Inicio</span>
            <div class="user-area">
                <div class="avatar"><?= strtoupper(substr($nombreUsuario, 0, 2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge-role"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>
 
        <div class="content">
 
            <!-- mensaje de bienvenida -->
            <div class="welcome-bar">
                <div class="welcome-text">
                    <h3>Bienvenido, <?= htmlspecialchars(explode(' ', $nombreUsuario)[0]) ?></h3>
                    <p>Encuentra tu próxima propiedad entre nuestras opciones disponibles</p>
                </div>
                <a href="ver_propiedades.php" class="btn-primary">Ver todas las propiedades</a>
            </div>
 
            <!-- estadisticas -->
            <div class="stats">
                <div class="stat-card">
                    <p class="stat-label">Propiedades disponibles</p>
                    <p class="stat-value"><?= $total_disp ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">En venta</p>
                    <p class="stat-value"><?= $total_venta ?></p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">En alquiler</p>
                    <p class="stat-value"><?= $total_alquiler ?></p>
                </div>
            </div>
 
            <!-- tarjetas de propiedades -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Propiedades disponibles</span>
                </div>
 
                <div class="prop-grid">
                <?php if(mysqli_num_rows($props) == 0): ?>
                    <p style="color:var(--color-text-secondary);padding:20px 0;grid-column:1/-1">
                        No hay propiedades disponibles en este momento.
                    </p>
                <?php else: ?>
                    <?php while($p = mysqli_fetch_assoc($props)): ?>
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
                                    <p class="prop-price">$<?= number_format($p['precio'], 0, ',', '.') ?></p>
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