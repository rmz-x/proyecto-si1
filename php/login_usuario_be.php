<?php
// Inicia la sesión para manejar datos del usuario
session_start();
// Incluye el archivo de conexión a la base de datos
include 'conexion_be.php';
// Incluye el archivo para registrar actividades
require_once '../include/actividad.php';

// Obtiene y limpia el correo del formulario POST
$correo     = trim($_POST['correo']);
// Obtiene la contraseña del formulario POST
$contrasena = $_POST['contrasena'];

// Prepara y ejecuta la consulta para verificar usuario y contraseña
$stmt = $conexion->prepare(
    "SELECT id, correo, contrasena, rol, nombre FROM usuarios WHERE correo = ? AND contrasena = ?"
);
$stmt->execute([$correo, $contrasena]);

// Si encuentra exactamente un usuario (login exitoso)
if ($stmt->rowCount() === 1) {
    // Obtiene los datos del usuario
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtiene la IP del usuario para resetear intentos fallidos
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    // Resetea los intentos fallidos al login exitoso
    $conexion->prepare("DELETE FROM intentos_fallidos WHERE correo = ? AND ip = ?")
             ->execute([$correo, $ip]);

    // Guarda los datos del usuario en la sesión
    $_SESSION['usuario'] = $user['correo'];
    $_SESSION['rol']     = $user['rol'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nombre']  = $user['nombre'];

    // Registra la actividad de inicio de sesión
    registrarActividad($conexion, 'Inicio de sesión',
        "El usuario {$user['nombre']} ({$user['rol']}) inició sesión correctamente.");

    // Redirige según el rol del usuario
    switch ($user['rol']) {
        case 'administrador': header("location: ../dashboard.php");           break;
        case 'agente':        header("location: ../dashboard_agente.php");    break;
        case 'asistente':     header("location: ../dashboard_asistente.php"); break;
        default:              header("location: ../dashboard_usuario.php");   break;
    }
    exit();
}

// Si no encontró usuario, maneja el intento fallido
// Obtiene la IP del usuario
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Verifica si ya hay intentos fallidos para este correo e IP
$stmtCheck = $conexion->prepare("SELECT intentos FROM intentos_fallidos WHERE correo = ? AND ip = ?");
$stmtCheck->execute([$correo, $ip]);
$existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

// Si ya existe, incrementa el contador
if ($existing) {
    $newIntentos = $existing['intentos'] + 1;
    $conexion->prepare("UPDATE intentos_fallidos SET intentos = ?, ultima_fecha = CURRENT_TIMESTAMP WHERE correo = ? AND ip = ?")
             ->execute([$newIntentos, $correo, $ip]);
} else {
    // Si no existe, crea un nuevo registro
    $conexion->prepare("INSERT INTO intentos_fallidos (correo, ip, intentos) VALUES (?, ?, 1)")
             ->execute([$correo, $ip]);
}

// Registra el intento fallido en la actividad
$stmtFail = $conexion->prepare(
    "INSERT INTO registro_actividad (usuario_id, accion, descripcion, ip) VALUES (?, ?, ?, ?)"
);
$usuario_id = null; // No sabemos el ID si no existe el usuario
$accion = 'Intento de sesión fallido';
$desc   = "Intento de inicio de sesión fallido con correo: $correo";
$stmtFail->execute([$usuario_id, $accion, $desc, $ip]);

// Verifica si ha alcanzado 5 intentos para forzar recuperación de contraseña
$stmtCount = $conexion->prepare("SELECT intentos FROM intentos_fallidos WHERE correo = ? AND ip = ?");
$stmtCount->execute([$correo, $ip]);
$count = $stmtCount->fetch(PDO::FETCH_ASSOC);

if ($count && $count['intentos'] >= 5) {
    // Muestra alerta y redirige a recuperación de contraseña
    echo '<script>
        alert("Demasiados intentos fallidos. Usa la opción de recuperar contraseña.");
        window.location = "../recuperar_contrasena.php";
    </script>';
} else {
    // Muestra alerta de credenciales incorrectas
    echo '<script>
        alert("Correo o contraseña incorrectos. Verifica tus datos.");
        window.location = "../index.php";
    </script>';
}

?>