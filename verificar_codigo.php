<?php
// Inicia la sesión para manejar datos del usuario
session_start();
// Incluye la conexión a la base de datos
include 'php/conexion_be.php';
// Incluye la función para registrar actividades
require_once 'include/actividad.php';

// Variable para mensajes de error o éxito
$message = '';
// Obtiene el ID del código de recuperación desde la URL
$codigo_id = $_GET['codigo_id'] ?? null;

// Si no hay código_id, redirige a la página de recuperación
if (!$codigo_id) {
    header("Location: recuperar_contrasena.php");
    exit();
}

// Verifica si se envió el formulario por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos del formulario
    $codigo = trim($_POST['codigo']);
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar = $_POST['confirmar'];

    // Valida que las contraseñas coincidan
    if ($nueva_contrasena !== $confirmar) {
        $message = 'Las contraseñas no coinciden.';
    // Valida que la contraseña tenga al menos 6 caracteres
    } elseif (strlen($nueva_contrasena) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Verifica el código en la base de datos (debe ser válido, no usado y no expirado)
        $stmt = $conexion->prepare("SELECT cr.*, u.correo, u.nombre, u.rol FROM codigos_recuperacion cr JOIN usuarios u ON cr.usuario_id = u.id WHERE cr.id = ? AND cr.usado = FALSE AND cr.expiracion > CURRENT_TIMESTAMP");
        $stmt->execute([$codigo_id]);
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el código no es válido o expiró
        if (!$rec || $rec['codigo'] !== $codigo) {
            $message = 'Código inválido o expirado.';
        } else {
            // Actualiza la contraseña del usuario
            $stmtUpdate = $conexion->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            $stmtUpdate->execute([$nueva_contrasena, $rec['usuario_id']]);

            // Marca el código como usado para evitar reutilización
            $conexion->prepare("UPDATE codigos_recuperacion SET usado = TRUE WHERE id = ?")
                     ->execute([$codigo_id]);

            // Resetea los intentos fallidos para este usuario
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $conexion->prepare("DELETE FROM intentos_fallidos WHERE correo = ?")
                     ->execute([$rec['correo']]);

            // Registra la actividad de cambio de contraseña
            registrarActividad($conexion, 'Contraseña cambiada',
                "El usuario {$rec['nombre']} cambió su contraseña vía recuperación.");

            // Hace login automático con los datos del usuario
            $_SESSION['usuario'] = $rec['correo'];
            $_SESSION['rol'] = $rec['rol']; // Asigna el rol real del usuario
            $_SESSION['user_id'] = $rec['usuario_id'];
            $_SESSION['nombre'] = $rec['nombre'];

            // Redirige al dashboard correspondiente según el rol
            switch ($rec['rol']) {
                case 'administrador':
                    header("Location: dashboard.php");
                    break;
                case 'agente':
                    header("Location: dashboard_agente.php");
                    break;
                case 'asistente':
                    header("Location: dashboard_asistente.php");
                    break;
                default:
                    header("Location: dashboard_usuario.php");
                    break;
            }
            exit();
        }
    }
}
?>
<!-- Inicio del HTML para la página de verificación -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código — Lorent Inmobiliaria</title>
    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS principales -->
    <link rel="stylesheet" href="asscet/css/estilos.css">
    <!-- Estilos inline para el formulario -->
    <style>
        .verify-form{max-width:400px;margin:50px auto;padding:20px;border:1px solid #ddd;border-radius:8px;background:#fff}
        .verify-form h2{text-align:center;margin-bottom:20px}
        .form-group{margin-bottom:15px}
        .form-group label{display:block;margin-bottom:5px}
        .form-group input{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px}
        .btn-submit{width:100%;padding:10px;background:#28a745;color:#fff;border:none;border-radius:4px;cursor:pointer}
        .btn-submit:hover{background:#218838}
        .message{text-align:center;margin-top:10px;color:red}
    </style>
</head>
<body>
    <!-- Contenedor del formulario de verificación -->
    <div class="verify-form">
        <h2>Verificar Código</h2>
        <!-- Formulario para ingresar código y nueva contraseña -->
        <form method="POST">
            <div class="form-group">
                <label for="codigo">Código de 6 dígitos</label>
                <!-- Campo para el código -->
                <input type="text" id="codigo" name="codigo" maxlength="6" required>
            </div>
            <div class="form-group">
                <label for="nueva_contrasena">Nueva Contraseña</label>
                <!-- Campo para la nueva contraseña -->
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
            </div>
            <div class="form-group">
                <label for="confirmar">Confirmar Nueva Contraseña</label>
                <!-- Campo para confirmar la nueva contraseña -->
                <input type="password" id="confirmar" name="confirmar" required>
            </div>
            <!-- Botón para enviar el formulario -->
            <button type="submit" class="btn-submit">Cambiar Contraseña</button>
        </form>
        <!-- Muestra mensajes de error o éxito -->
        <p class="message"><?php echo $message; ?></p>
        <!-- Enlace para volver a la página de recuperación -->
        <p style="text-align:center;margin-top:20px"><a href="recuperar_contrasena.php">Volver</a></p>
    </div>
</body>
</html>