<?php
// Obtiene el nombre del archivo actual para resaltar el menú activo
$current = basename($_SERVER['PHP_SELF']);
// Obtiene el rol del usuario de la sesión, por defecto 'cliente'
$rol     = $_SESSION['rol'] ?? 'cliente';
?>
<!-- Barra lateral de navegación personalizada por rol -->
<aside class="sidebar">
    <!-- Logo de la empresa -->
    <div class="logo">
        <span class="logo-title">Lorent</span>
        <span class="logo-sub">Inmobiliaria</span>
    </div>
    <!-- Navegación dinámica según el rol -->
    <nav class="nav">

        <!-- Si el usuario es agente, muestra menú de agente -->
        <?php if ($rol === 'agente'): ?>
        <!-- MENÚ AGENTE -->
        <p class="nav-section">Gestión</p>
        <!-- Enlace al dashboard del agente -->
        <a href="dashboard_agente.php"   class="nav-item <?= $current=='dashboard_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <!-- Enlace a propiedades del agente -->
        <a href="propiedades_agente.php" class="nav-item <?= $current=='propiedades_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#9FE1CB"></span>Mis propiedades
        </a>
        <!-- Enlace a visitas del agente -->
        <a href="visitas_agente.php"     class="nav-item <?= $current=='visitas_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#FAC775"></span>Visitas
        </a>
        <!-- Enlace a clientes del agente -->
        <a href="clientes_agente.php"    class="nav-item <?= $current=='clientes_agente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#F4C0D1"></span>Clientes
        </a>

        <!-- Si el usuario es asistente, muestra menú de asistente -->
        <?php elseif ($rol === 'asistente'): ?>
        <!-- MENÚ ASISTENTE -->
        <p class="nav-section">Operaciones</p>
        <!-- Enlace al dashboard del asistente -->
        <a href="dashboard_asistente.php" class="nav-item <?= $current=='dashboard_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <!-- Enlace a clientes del asistente -->
        <a href="clientes_asistente.php"  class="nav-item <?= $current=='clientes_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#F4C0D1"></span>Clientes
        </a>
        <!-- Enlace a visitas del asistente -->
        <a href="visitas_asistente.php"   class="nav-item <?= $current=='visitas_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#FAC775"></span>Agenda / Visitas
        </a>
        <!-- Enlace a reportes del asistente -->
        <a href="reportes_asistente.php"  class="nav-item <?= $current=='reportes_asistente.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#AFA9EC"></span>Reportes
        </a>

        <!-- Si no es agente ni asistente, muestra menú de cliente -->
        <?php else: ?>
        <!-- MENÚ CLIENTE (default) -->
        <p class="nav-section">Menú</p>
        <!-- Enlace al dashboard del cliente -->
        <a href="dashboard_usuario.php"  class="nav-item <?= $current=='dashboard_usuario.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#64b5f6"></span>Inicio
        </a>
        <!-- Enlace a ver propiedades -->
        <a href="ver_propiedades.php"    class="nav-item <?= $current=='ver_propiedades.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
        </a>
        <!-- Enlace a mis solicitudes -->
        <a href="mis_solicitudes.php"    class="nav-item <?= $current=='mis_solicitudes.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#FAC775"></span>Mis solicitudes
        </a>
        <?php endif; ?>

        <!-- Sección Cuenta (común para todos) -->
        <p class="nav-section">Cuenta</p>
        <!-- Enlace a mi perfil -->
        <a href="mi_perfil.php" class="nav-item <?= $current=='mi_perfil.php'?'active':'' ?>">
            <span class="nav-dot" style="background:#AFA9EC"></span>Mi perfil
        </a>

    </nav>
    <!-- Enlace para cerrar sesión -->
    <a href="php/cerrar_sesion.php" class="nav-logout">↩ Cerrar sesión</a>
</aside>