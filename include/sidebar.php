<?php
/* incluir dentro de cada página del panel */
// Obtiene el nombre del archivo actual para resaltar el menú activo
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- Barra lateral de navegación -->
<aside class="sidebar">
    <!-- Logo de la empresa -->
    <div class="logo">
        <span class="logo-title">Lorent</span>
        <span class="logo-sub">Inmobiliaria</span>
    </div>
    <!-- Navegación principal -->
    <nav class="nav">
        <!-- Sección Principal -->
        <p class="nav-section">Principal</p>
        <!-- Enlace al Dashboard, activo si es la página actual -->
        <a href="dashboard.php"   class="nav-item <?= $current=='dashboard.php'   ?'active':'' ?>">
            <span class="nav-dot" style="background:#46A2FD"></span>Dashboard
        </a>
        <!-- Sección Gestión -->
        <p class="nav-section">Gestión</p>
        <!-- Enlace a Propiedades -->
        <a href="propiedades.php" class="nav-item <?= $current=='propiedades.php' ?'active':'' ?>">
            <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
        </a>
        <!-- Sección Admin -->
        <p class="nav-section">Admin</p>
        <!-- Enlace a Usuarios -->
        <a href="usuarios.php"    class="nav-item <?= $current=='usuarios.php'    ?'active':'' ?>">
            <span class="nav-dot" style="background:#AFA9EC"></span>Usuarios
        </a>
        <!-- Enlace a Actividad de Usuarios -->
        <a href="actividad_usuarios.php" class="nav-item <?= $current=='actividad_usuarios.php' ?'active':'' ?>">
            <span class="nav-dot" style="background:#F4A261"></span>Actividad de Usuarios
        </a>
    </nav>
    <!-- Enlace para cerrar sesión -->
    <a href="php/cerrar_sesion.php" class="nav-logout">↩ Cerrar sesión</a>
</aside>
