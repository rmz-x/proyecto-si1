<?php
/* agregar, editar y eliminar usuarios */
session_start();
if (!isset($_SESSION['usuario'])) {
    header("location: ../index.php");
    exit();
}
include 'conexion_be.php';

// eliminar
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $id = (int)$_GET['id'];

    // no permitir eliminar al usuario actual
    $emailActual = $_SESSION['usuario'];
    $check = $conexion->prepare("SELECT correo FROM usuarios WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($correoUsuario);
    $check->fetch();
    $check->close();

    if ($correoUsuario === $emailActual) {
        echo '<script>alert("No puedes eliminar tu propia cuenta.");window.location="../usuarios.php";</script>';
        exit();
    }

    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo '<script>alert("Usuario eliminado correctamente.");window.location="../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al eliminar el usuario.");window.location="../usuarios.php";</script>';
    }
    $stmt->close();
    exit();
}

// agregar
if ($_POST['accion'] === 'agregar') {
    $nombre   = trim($_POST['nombre']);
    $correo   = trim($_POST['correo']);
    $usuario  = trim($_POST['usuario']);
    $pass     = $_POST['contrasena'];
    $rol      = $_POST['rol'];

    // verificar correo duplicado
    $ck = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $ck->bind_param("s", $correo);
    $ck->execute();
    $ck->store_result();
    if ($ck->num_rows > 0) {
        echo '<script>alert("Ese correo ya está registrado.");window.location="../usuarios.php";</script>';
        exit();
    }
    $ck->close();

    // verificar usuario duplicado
    $ck2 = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $ck2->bind_param("s", $usuario);
    $ck2->execute();
    $ck2->store_result();
    if ($ck2->num_rows > 0) {
        echo '<script>alert("Ese nombre de usuario ya existe.");window.location="../usuarios.php";</script>';
        exit();
    }
    $ck2->close();

    $stmt = $conexion->prepare(
        "INSERT INTO usuarios (nombre, correo, usuario, contrasena, rol) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssss", $nombre, $correo, $usuario, $pass, $rol);
    if ($stmt->execute()) {
        echo '<script>alert("Usuario agregado exitosamente.");window.location="../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al agregar el usuario.");window.location="../usuarios.php";</script>';
    }
    $stmt->close();
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
        $stmt->bind_param("sssssi", $nombre, $correo, $usuario, $pass, $rol, $id);
    } else {
        // no cambiar la contraseña
        $stmt = $conexion->prepare(
            "UPDATE usuarios SET nombre=?, correo=?, usuario=?, rol=? WHERE id=?"
        );
        $stmt->bind_param("ssssi", $nombre, $correo, $usuario, $rol, $id);
    }

    if ($stmt->execute()) {
        echo '<script>alert("Usuario actualizado correctamente.");window.location="../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar el usuario.");window.location="../usuarios.php";</script>';
    }
    $stmt->close();
}

mysqli_close($conexion);