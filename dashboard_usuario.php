<?php

require_once 'include/auth_check.php';
include 'php/conexion_be.php';

// Redirección si es administrador
if ($_SESSION['rol'] === 'administrador') {
    header("Location: dashboard.php");
    exit();
}

// Estadísticas
$total_disp = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible'")->fetch(PDO::FETCH_ASSOC)['n'];
$total_venta = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible' AND tipo='Venta'")->fetch(PDO::FETCH_ASSOC)['n'];
$total_alquiler = $conexion->query("SELECT COUNT(*) AS n FROM propiedades WHERE estado='Disponible' AND tipo='Alquiler'")->fetch(PDO::FETCH_ASSOC)['n'];

// 🔹 Búsqueda dinámica centralizada
include 'php/buscar_propiedades_be.php';

// Nombre del usuario
$stmtUser = $conexion->prepare("SELECT nombre FROM usuarios WHERE correo=:correo");
$stmtUser->execute([':correo' => $_SESSION['usuario']]);
$infoUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
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



            <!-- buscar propiedades -->
            <form method="GET" action="dashboard_usuario.php" class="search-bar">
                <input type="text" name="q" placeholder="Buscar por título o zona"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <select name="tipo">
                    <option value="">Todos</option>
                    <option value="Venta" <?= ($_GET['tipo'] ?? '')=='Venta'?'selected':'' ?>>Venta</option>
                    <option value="Alquiler" <?= ($_GET['tipo'] ?? '')=='Alquiler'?'selected':'' ?>>Alquiler</option>
                    <option value="Anticretico" <?= ($_GET['tipo'] ?? '')=='Anticretico'?'selected':'' ?>>Anticrético</option>
                </select>
                <button type="submit">Buscar</button>
            </form>

            <!-- filtros avanzados -->
            <form method="GET" action="dashboard_usuario.php" class="filter-bar">
                <input type="text" name="zona" placeholder="Zona"
                    value="<?= htmlspecialchars($_GET['zona'] ?? '') ?>">

                <input type="number" name="precio_min" placeholder="Precio mínimo"
                    value="<?= htmlspecialchars($_GET['precio_min'] ?? '') ?>">

                <input type="number" name="precio_max" placeholder="Precio máximo"
                    value="<?= htmlspecialchars($_GET['precio_max'] ?? '') ?>">

                <input type="number" name="area_min" placeholder="Área mínima (m²)"
                    value="<?= htmlspecialchars($_GET['area_min'] ?? '') ?>">

                <input type="number" name="area_max" placeholder="Área máxima (m²)"
                    value="<?= htmlspecialchars($_GET['area_max'] ?? '') ?>">

                <button type="submit">Aplicar filtros</button>
            </form>

            
            <!-- resumen de búsqueda -->
            <?php if (!empty($_GET)): ?>
                <p class="search-summary">
                    Mostrando <?= $props->rowCount() ?> resultados 
                    <?php if (!empty($_GET['q'])): ?>
                        para "<strong><?= htmlspecialchars($_GET['q']) ?></strong>"
                    <?php endif; ?>
                </p>
            <?php endif; ?>


            <!-- tarjetas de propiedades -->
            <?php if ($props->rowCount() == 0): ?>

                <div class="card">
                    <p style="padding:20px;color:#777">
                        <?php if (!empty($_GET)): ?>
                            No se encontraron propiedades con esos criterios.
                        <?php else: ?>
                            No hay propiedades disponibles en este momento.
                        <?php endif; ?>
                    </p>

                    <?php if (!empty($_GET['q'])): ?>
                        <p style="padding:0 20px;color:#999">
                            Mostrando 0 resultados para 
                            "<strong><?= htmlspecialchars($_GET['q']) ?></strong>"
                        </p>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Propiedades disponibles</span>
                    </div>

                    <div class="prop-grid">
                        <?php while ($p = $props->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="prop-card">
                                <div class="prop-img prop-img-<?= strtolower($p['tipo']) ?>">
                                    <span class="prop-tag tag-<?= strtolower($p['tipo']) ?>">
                                        <?= $p['tipo'] ?>
                                    </span>
                                </div>
                                <div class="prop-body">
                                    <p class="prop-title"><?= htmlspecialchars($p['titulo']) ?></p>
                                    <p class="prop-zona"><?= htmlspecialchars($p['zona']) ?></p>
                                    <div class="prop-footer">
                                        <p class="prop-price">
                                            $<?= number_format($p['precio'], 0, ',', '.') ?>
                                        </p>
                                        <a href="detalle_propiedad.php?id=<?= $p['id'] ?>" class="btn-detalle">
                                            Ver detalle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            <?php endif; ?>
            

 

        </div>
    </div>
</div>
</body>
</html>