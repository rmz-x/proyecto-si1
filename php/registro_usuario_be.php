<?php
// Incluye la conexión a la base de datos
include 'conexion_be.php';
// Incluye la función de validación de contraseña
include 'validar_contrasena.php';

// Obtiene y limpia los datos del formulario POST
$nombre    = trim($_POST['nombre']);     // Nombre completo del usuario
$correo    = trim($_POST['correo']);     // Correo electrónico
$usuario   = trim($_POST['usuario']);    // Nombre de usuario
$contrasena = $_POST['contrasena'];      // Contraseña
$rol       = 'cliente';                  // Rol por defecto para nuevos usuarios

// Valida la contraseña
$validacion = validarContrasena($contrasena);
if (!$validacion['valida']) {
    // Si la contraseña no es válida, muestra los errores
    $mensajes = implode('\n', $validacion['errores']);
    echo '<script>alert("Errores en la contraseña:\n' . $mensajes . '");window.location="../index.php";</script>';
    exit();
}

// Verifica si el correo ya existe en la base de datos
$ck1 = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
$ck1->execute([$correo]);
if ($ck1->rowCount() > 0) {
    // Si existe, muestra alerta y redirige al login
    echo '<script>alert("Este correo ya existe.");window.location="../index.php";</script>';
    exit();
}

// Verifica si el nombre de usuario ya existe
$ck2 = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$ck2->execute([$usuario]);
if ($ck2->rowCount() > 0) {
    // Si existe, muestra alerta y redirige al login
    echo '<script>alert("Este nombre de usuario ya existe.");window.location="../index.php";</script>';
    exit();
}

// Prepara la consulta para insertar el nuevo usuario
$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre, correo, usuario, contrasena, rol) VALUES (?, ?, ?, ?, ?)"
);

// Ejecuta la inserción y verifica si fue exitosa
if ($stmt->execute([$nombre, $correo, $usuario, $contrasena, $rol])) {
    // Si se registró correctamente, muestra mensaje de éxito
    echo '<script>alert("Usuario registrado exitosamente.");window.location="../index.php";</script>';
} else {
    // Si hubo error, muestra mensaje de error
    echo '<script>alert("Error al registrar el usuario.");window.location="../index.php";</script>';
}