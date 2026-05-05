<?php
// Verifica que el usuario esté logueado
require_once 'include/auth_check.php';
// Incluye conexión a BD
include 'php/conexion_be.php';

// Obtiene el ID de la propiedad desde GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Consulta la propiedad con el agente asignado
$stmt = $conexion->prepare(
    "SELECT p.*, u.nombre AS agente_nombre
     FROM propiedades p
     LEFT JOIN usuarios u ON p.agente_id = u.id
     WHERE p.id = ?"
);
$stmt->execute([$id]);
$prop = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe la propiedad, redirige
if (!$prop) {
    echo '<script>alert("Propiedad no encontrada.");window.location="ver_propiedades.php";</script>';
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($prop['titulo']) ?> — Lorent</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/dashboard_usuario.css">
    <style>
        .detalle-hero{height:180px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;position:relative}
        .hero-venta      {background:#1565c0}
        .hero-alquiler   {background:#0f6e56}
        .hero-anticretico{background:#6a1b9a}
        .hero-tag{position:absolute;top:12px;left:12px;font-size:12px;padding:4px 12px;border-radius:10px;font-weight:500}
        .tag-venta{background:#e8f5e9;color:#2e7d32}
        .tag-alquiler{background:#fff8e1;color:#e65100}
        .tag-anticretico{background:#f3e5f5;color:#6a1b9a}
        .hero-noimg{color:rgba(255,255,255,0.3);font-size:14px}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px}
        .info-item label{font-size:11px;color:#6c757d;display:block;margin-bottom:3px}
        .info-item p{font-size:14px;font-weight:500}
        .precio-grande{font-size:28px;font-weight:700;color:#185FA5}
        .divider{border:none;border-top:1px solid #e2e6ea;margin:20px 0}
        .form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
        .form-group label{font-size:12px;color:#6c757d;font-weight:500}
        .form-group input,.form-group textarea{padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#f8f9fa}
        .form-group input:focus,.form-group textarea:focus{border-color:#64b5f6;background:#fff}
        .badge-estado{display:inline-block;font-size:12px;padding:4px 12px;border-radius:10px;font-weight:500}
        .badge-disponible{background:#e8f5e9;color:#2e7d32}
        .badge-reservado{background:#fff8e1;color:#e65100}
        .badge-vendido{background:#ffebee;color:#c62828}
    </style>
</head>
<body>
<div class="layout">
    <?php include 'include/sidebar_usuario.php'; ?>
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Detalle de propiedad</span>
            <div class="user-area">
                <div class="avatar"><?= strtoupper(substr($nombreUsuario,0,2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($nombreUsuario) ?></span>
                <span class="badge-role"><?= ucfirst($_SESSION['rol']) ?></span>
            </div>
        </div>
        <div class="content">
            <div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

                <!-- columna izquierda -->
                <div class="card">
                    <div class="detalle-hero hero-<?= strtolower($prop['tipo']) ?>">
                        <span class="hero-noimg">Sin foto disponible</span>
                        <span class="hero-tag tag-<?= strtolower($prop['tipo']) ?>"><?= $prop['tipo'] ?></span>
                    </div>

                    <h2 style="font-size:20px;font-weight:600;color:#0f4c75;margin-bottom:6px">
                        <?= htmlspecialchars($prop['titulo']) ?>
                    </h2>
                    <p style="color:#6c757d;font-size:13px;margin-bottom:16px">
                        <?= htmlspecialchars($prop['zona']) ?>
                    </p>

                    <div class="info-grid">
                        <div class="info-item">
                            <label>Precio</label>
                            <p class="precio-grande">$<?= number_format($prop['precio'],0,',','.') ?></p>
                        </div>
                        <div class="info-item">
                            <label>Estado</label>
                            <p><span class="badge-estado badge-<?= strtolower($prop['estado']) ?>"><?= $prop['estado'] ?></span></p>
                        </div>
                        <div class="info-item">
                            <label>Área</label>
                            <p><?= $prop['area'] ? $prop['area'].' m²' : '—' ?></p>
                        </div>
                        <div class="info-item">
                            <label>Agente responsable</label>
                            <p><?= htmlspecialchars($prop['agente_nombre'] ?? 'Sin asignar') ?></p>
                        </div>
                    </div>

                    <hr class="divider">
                    <p style="font-size:13px;font-weight:500;color:#0f4c75;margin-bottom:8px">Descripción</p>
                    <p style="font-size:13px;color:#444;line-height:1.7">
                        <?= nl2br(htmlspecialchars($prop['descripcion'] ?? 'Sin descripción.')) ?>
                    </p>

                    <div style="margin-top:20px">
                        <a href="ver_propiedades.php" class="btn-detalle">← Volver a propiedades</a>
                    </div>
                </div>

                <!-- columna derecha solicitar visita (solo clientes) -->
                <?php if($_SESSION['rol'] === 'cliente'): ?>
                <div class="card">
                    <p style="font-size:15px;font-weight:600;color:#0f4c75;margin-bottom:16px">Solicitar visita</p>
                    <form action="php/solicitar_visita_be.php" method="POST">
                        <input type="hidden" name="propiedad_id" value="<?= $prop['id'] ?>">
                        <div class="form-group">
                            <label>Fecha preferida</label>
                            <input type="date" name="fecha" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mensaje al agente</label>
                            <textarea name="mensaje" rows="4" placeholder="Escribe tus preferencias o preguntas..." required></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="width:100%">Enviar solicitud</button>
                    </form>
                </div>
                <?php else: ?>
                <div class="card" style="text-align:center;color:#6c757d;padding:30px">
                    <p style="font-size:13px">Solo los clientes pueden solicitar visitas.</p>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<script src="asscet/js/validaciones.js"></script>
</body>
</html>