<?php
session_start();
include 'conexion_be.php';
require_once '../include/actividad.php';

$correo     = trim($_POST['correo']);
$contrasena = $_POST['contrasena'];

$stmt = $conexion->prepare(
    "SELECT id, correo, contrasena, rol, nombre FROM usuarios WHERE correo = ? AND contrasena = ?"
);
$stmt->bind_param("ss", $correo, $contrasena);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $user = $resultado->fetch_assoc();

    $_SESSION['usuario'] = $user['correo'];
    $_SESSION['rol']     = $user['rol'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nombre']  = $user['nombre'];

    // registrar inicio de sesión
    registrarActividad($conexion, 'Inicio de sesión',
        "El usuario {$user['nombre']} ({$user['rol']}) inició sesión correctamente.");

    $stmt->close();

    switch ($user['rol']) {
        case 'administrador': header("location: ../dashboard.php");           break;
        case 'agente':        header("location: ../dashboard_agente.php");    break;
        case 'asistente':     header("location: ../dashboard_asistente.php"); break;
        default:              header("location: ../dashboard_usuario.php");   break;
    }
    exit();
}

// registrar intento fallido
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$stmtFail = $conexion->prepare(
    "INSERT INTO registro_actividad (correo, accion, descripcion, ip) VALUES (?, ?, ?, ?)"
);
$accion = 'Intento de sesión fallido';
$desc   = "Intento de inicio de sesión fallido con correo: $correo";
$stmtFail->bind_param("ssss", $correo, $accion, $desc, $ip);
$stmtFail->execute();
$stmtFail->close();

echo '<script>
    alert("Correo o contraseña incorrectos. Verifica tus datos.");
    window.location = "../index.php";
</script>';

$stmt->close();
mysqli_close($conexion);