<?php
/* incluir dentro de cada página del panel */
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="logo">
        <span class="logo-title">Lorent</span>
        <span class="logo-sub">Inmobiliaria</span>
    </div>
    <nav class="nav">
        <p class="nav-section">Principal</p>
        <a href="dashboard.php"   class="nav-item <?= $current=='dashboard.php'   ?'active':'' ?>">
            <span class="nav-dot" style="background:#46A2FD"></span>Dashboard
        </a>
        <p class="nav-section">Gestión</p>
        <a href="propiedades.php" class="nav-item <?= $current=='propiedades.php' ?'active':'' ?>">
            <span class="nav-dot" style="background:#9FE1CB"></span>Propiedades
        </a>
        <p class="nav-section">Admin</p>
        <a href="usuarios.php"    class="nav-item <?= $current=='usuarios.php'    ?'active':'' ?>">
            <span class="nav-dot" style="background:#AFA9EC"></span>Usuarios
        </a>
    </nav>
    <a href="php/cerrar_sesion.php" class="nav-logout">↩ Cerrar sesión</a>
</aside>
