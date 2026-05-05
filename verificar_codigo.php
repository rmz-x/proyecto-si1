<?php
// Inicia la sesión para manejar datos del usuario
session_start();
// Incluye la conexión a la base de datos
include 'php/conexion_be.php';
// Incluye la función para registrar actividades
require_once 'include/actividad.php';
// Incluye la función de validación de contraseña
include 'php/validar_contrasena.php';

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
    } else {
        // Valida el formato de la contraseña
        $validacion = validarContrasena($nueva_contrasena);
        if (!$validacion['valida']) {
            $message = 'Errores en la contraseña: ' . implode(', ', $validacion['errores']);
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
    <!-- Script para validaciones y funciones comunes -->
    <script src="asscet/js/validaciones.js"></script>
    <!-- Estilos inline para el formulario -->
    <style>
        .verify-form{max-width:400px;margin:50px auto;padding:20px;border:1px solid #ddd;border-radius:8px;background:#fff}
        .verify-form h2{text-align:center;margin-bottom:20px}
        .form-group{margin-bottom:15px}
        .form-group label{display:block;margin-bottom:5px}
        .form-group input{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px}
        .password-wrapper { position:relative; display:flex; align-items:center; width:100%; margin-top:0; }
        .password-wrapper input { width:100%; padding:10px; padding-right:40px; border:1px solid #ccc; border-radius:4px; }
        .password-wrapper input:focus { background:#E8F1FF; border-color:#46A2FD; }
        .password-toggle-btn { position:absolute; right:8px; background:none; border:none; cursor:pointer; font-size:16px; padding:5px 8px; outline:none; transition:transform 200ms; }
        .password-toggle-btn:hover { transform:scale(1.1); }
        .btn-submit{width:100%;padding:10px;background:#28a745;color:#fff;border:none;border-radius:4px;cursor:pointer;margin-top:15px}
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
                <!-- Campo para la nueva contraseña con toggle -->
                <div class="password-wrapper">
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
                    <button class="password-toggle-btn" title="Mostrar contraseña">👁️</button>
                </div>
            </div>
            <div class="form-group">
                <label for="confirmar">Confirmar Nueva Contraseña</label>
                <!-- Campo para confirmar con toggle -->
                <div class="password-wrapper">
                    <input type="password" id="confirmar" name="confirmar" required>
                    <button class="password-toggle-btn" title="Mostrar contraseña">👁️</button>
                </div>
            </div>
            <!-- Botón para enviar el formulario -->
            <button type="submit" class="btn-submit">Cambiar Contraseña</button>
        </form>
        <!-- Muestra mensajes de error o éxito -->
        <p class="message"><?php echo $message; ?></p>
        <!-- Enlace para volver a la página de recuperación -->
        <p style="text-align:center;margin-top:20px"><a href="recuperar_contrasena.php">Volver</a></p>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Inicializar toggle de contraseña
            inicializarTogglePassword();
            
            const form = document.querySelector('form');
            const codigoInput = document.getElementById('codigo');
            const nuevaContraInput = document.getElementById('nueva_contrasena');
            const confirmarInput = document.getElementById('confirmar');

            // Evento de validación del formulario
            form.addEventListener('submit', function (e) {
                const codigo = codigoInput.value.trim();
                const nuevaContra = nuevaContraInput.value;
                const confirmar = confirmarInput.value;
                let errores = [];

                // Validar código
                if (codigo === '') {
                    errores.push("El código es obligatorio.");
                } else if (!/^\d{6}$/.test(codigo)) {
                    errores.push("El código debe ser exactamente 6 dígitos.");
                }

                // Validar que las contraseñas coincidan
                if (nuevaContra !== confirmar) {
                    errores.push("Las contraseñas no coinciden.");
                }

                // Validar formato de contraseña
                if (nuevaContra !== '') {
                    const validacion = validarFormatoContrasena(nuevaContra);
                    if (!validacion.valida) {
                        errores = errores.concat(validacion.errores);
                    }
                } else {
                    errores.push("La contraseña es obligatoria.");
                }

                // Si hay errores, mostrarlos y prevenir envío
                if (errores.length > 0) {
                    const messageDiv = document.querySelector('.message');
                    messageDiv.innerHTML = errores.join('<br>');
                    messageDiv.style.color = '#dc3545';
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.border = '1px solid #f5c6cb';
                    messageDiv.style.padding = '10px';
                    messageDiv.style.borderRadius = '4px';
                    e.preventDefault();
                }
            });

            // Limpiar mensajes de error al escribir
            [codigoInput, nuevaContraInput, confirmarInput].forEach(input => {
                input.addEventListener('input', function () {
                    const messageDiv = document.querySelector('.message');
                    if (messageDiv.innerHTML.trim() !== '') {
                        messageDiv.innerHTML = '';
                    }
                });
            });
        });
    </script>
</body>
</html>