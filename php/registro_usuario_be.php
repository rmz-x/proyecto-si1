<?php
include 'conexion_be.php';

$nombre    = trim($_POST['nombre']);
$correo    = trim($_POST['correo']);
$usuario   = trim($_POST['usuario']);
$contrasena = $_POST['contrasena'];
$rol       = 'cliente'; 

// verificar correo duplicado
$ck1 = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
$ck1->bind_param("s", $correo);
$ck1->execute();
$ck1->store_result();
if ($ck1->num_rows > 0) {
    echo '<script>alert("Este correo ya existe.");window.location="../index.php";</script>';
    exit();
}
$ck1->close();

// verificar usuario duplicado
$ck2 = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$ck2->bind_param("s", $usuario);
$ck2->execute();
$ck2->store_result();
if ($ck2->num_rows > 0) {
    echo '<script>alert("Este nombre de usuario ya existe.");window.location="../index.php";</script>';
    exit();
}
$ck2->close();

// insertar con rol por defecto 'cliente'
$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre, correo, usuario, contrasena, rol) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssss", $nombre, $correo, $usuario, $contrasena, $rol);

if ($stmt->execute()) {
    echo '<script>alert("Usuario registrado exitosamente.");window.location="../index.php";</script>';
} else {
    echo '<script>alert("Error al registrar el usuario.");window.location="../index.php";</script>';
}
$stmt->close();
mysqli_close($conexion);