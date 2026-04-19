<?php
$current = basename($_SERVER['PHP_SELF']);
$rol     = $_SESSION['rol'] ?? 'cliente';
?>
<aside class="sidebar">
    <div class="logo">
        <span class="logo-title">Lorent</span>
        <span class="logo-sub">Inmobiliaria</span>
    </div>
    <nav class="nav">

        <?php if ($rol === 'agente'): ?>
        <!-- MENÚ AGENTE -->
        <p class="nav-section">Gestión</p>
        <a href="dashboard_agente.php"   class="nav-item <?= $current=='dashboard_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <a href="propiedades_agente.php" class="nav-item <?= $current=='propiedades_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#9FE1CB"></span>Mis propiedades
        </a>
        <a href="visitas_agente.php"     class="nav-item <?= $current=='visitas_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#FAC775"></span>Visitas
        </a>
        <a href="clientes_agente.php"    class="nav-item <?= $current=='clientes_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#F4C0D1"></span>Clientes
        </a>

        <?php elseif ($rol === 'asistente'): ?>
        <!-- MENÚ ASISTENTE -->
        <p class="nav-section">Operaciones</p>
        <a href="dashboard_asistente.php" class="nav-item <?= $current=='dashboard_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <a href="clientes_asistente.php"  class="nav-item <?= $current=='clientes_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#F4C0D1"></span>Clientes
        </a>
        <a href="visitas_asistente.php"   class="nav-item <?= $current=='visitas_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#FAC775"></span>Agenda / Visitas
        </a>
        <a href="reportes_asistente.php"  class="nav-item <?= $current=='reportes_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#AFA9EC"></span>Reportes
        </a>

        <?php else: ?>
        <!-- MENÚ CLIENTE (default) -->
        <p class="nav-section">Menú</p>
        <a href="dashboard_usuario.php"  class="nav-item <?= $current=='dashboard_usuario.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <a href="ver_propiedades.php"    class="nav-item <?= $current=='ver_propiedades.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
        </a>
        <a href="mis_solicitudes.php"    class="nav-item <?= $current=='mis_solicitudes.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#FAC775"></span>Mis solicitudes
        </a>
        <?php endif; ?>

        <p class="nav-section">Cuenta</p>
        <a href="mi_perfil.php" class="nav-item <?= $current=='mi_perfil.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#AFA9EC"></span>Mi perfil
        </a>

    </nav>
    <a href="php/cerrar_sesion.php" class="nav-logout">↩ Cerrar sesión</a>
</aside>