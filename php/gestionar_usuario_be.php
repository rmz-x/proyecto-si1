<?php
/* Agregar, editar y eliminar usuarios */
// Inicia sesión
session_start();
// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("location: ../index.php");
    exit();
}
// Incluye conexión a BD
include 'conexion_be.php';

// Eliminar usuario
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $id = (int)$_GET['id'];

    // No permitir eliminar al usuario actual
    $emailActual = $_SESSION['usuario'];
    $check = $conexion->prepare("SELECT correo FROM usuarios WHERE id = ?");
    $check->execute([$id]);
    $correoUsuario = $check->fetch(PDO::FETCH_ASSOC)['correo'] ?? null;

    if ($correoUsuario === $emailActual) {
        echo '<script>alert("No puedes eliminar tu propia cuenta.");window.location="../usuarios.php";</script>';
        exit();
    }

    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo '<script>alert("Usuario eliminado correctamente.");window.location="../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al eliminar el usuario.");window.location="../usuarios.php";</script>';
    }
    exit();
}

// Agregar usuario
if ($_POST['accion'] === 'agregar') {
    $nombre   = trim($_POST['nombre']);
    $correo   = trim($_POST['correo']);
    $usuario  = trim($_POST['usuario']);
    $pass     = $_POST['contrasena'];
    $rol      = $_POST['rol'];

    // Verificar correo duplicado
    $ck = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $ck->execute([$correo]);
    if ($ck->rowCount() > 0) {
        echo '<script>alert("Ese correo ya está registrado.");window.location="../usuarios.php";</script>';
        exit();
    }

    // Verificar usuario duplicado
    $ck2 = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $ck2->execute([$usuario]);
    if ($ck2->rowCount() > 0) {
        echo '<script>alert("Ese nombre de usuario ya existe.");window.location="../usuarios.php";</script>';
        exit();
    }

    $stmt = $conexion->prepare(
        "INSERT INTO usuarios (nombre, correo, usuario, contrasena, rol) VALUES (?, ?, ?, ?, ?)"
    );
    if ($stmt->execute([$nombre, $correo, $usuario, $pass, $rol])) {
        echo '<script>alert("Usuario agregado exitosamente.");window.location="../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al agregar el usuario.");window.location="../usuarios.php";</script>';
    }
}

// editar
elseif ($_POST['accion'] === 'editar') {
    $id      = (int)$_POST['id'];
    $nombre  = trim($_POST['nombre']);
    $correo  = trim($_POST['correo']);
    $usuario = trim($_POST['usuario']);
    $rol     = $_POST['rol'];
    $pass    = $_POST['contrasena'];

    if ($pass !== '') {
        // actualizar también la contraseña SIN ENCRIPTAR
        $stmt = $conexion->prepare(
            "UPDATE usuarios SET nombre=?, correo=?, usuario=?, contrasena=?, rol=? WHERE id=?"
        );
        $stmt->execute([$nombre, $correo, $usuario, $pass, $rol, $id]);
    } else {
        // no cambiar la contraseña
        $stmt = $conexion->prepare(
            "UPDATE usuarios SET nombre=?, correo=?, usuario=?, rol=? WHERE id=?"
        );
        $stmt->execute([$nombre, $correo, $usuario, $rol, $id]);
    }

    if ($stmt->execute()) {
        echo '<script>alert("Usuario actualizado correctamente.");window.location="../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar el usuario.");window.location="../usuarios.php";</script>';
    }
}

?>