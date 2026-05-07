<?php
session_start();
include 'conexion_be.php';

// Solo agentes pueden registrar prospectos
if ($_SESSION['rol'] !== 'agente') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre       = trim($_POST['nombre'] ?? '');
    $correo       = trim($_POST['correo'] ?? '');
    $telefono     = trim($_POST['telefono'] ?? '');
    $propiedad_id = (int)($_POST['propiedad_id'] ?? 0);
    $mensaje      = trim($_POST['mensaje'] ?? '');
    $cliente_id   = $_POST['cliente_id'] ?? null;

    if ($nombre && $correo && $telefono && $propiedad_id) {
        try {
            $stmt = $conexion->prepare("
                INSERT INTO clientes_interesados 
                (cliente_id, nombre, correo, telefono, propiedad_id, mensaje, fecha_registro)
                VALUES (:cliente_id, :nombre, :correo, :telefono, :propiedad_id, :mensaje, NOW())
            ");
            $stmt->execute([
                ':cliente_id'   => $cliente_id,
                ':nombre'       => $nombre,
                ':correo'       => $correo,
                ':telefono'     => $telefono,
                ':propiedad_id' => $propiedad_id,
                ':mensaje'      => $mensaje
            ]);

            // Después de registrar cliente interesado
            // Insertar prospecto
            $stmtProspecto = $conexion->prepare("
                INSERT INTO prospectos (nombre, correo, telefono, estado, fecha_registro)
                VALUES (:nombre, :correo, :telefono, 'nuevo', NOW())
                RETURNING id
            ");
            $stmtProspecto->execute([
                ':nombre'   => $nombre,
                ':correo'   => $correo,
                ':telefono' => $telefono
            ]);
            $prospecto_id = $stmtProspecto->fetchColumn();

            // Relacionar prospecto con propiedad
            $stmtRelacion = $conexion->prepare("
                INSERT INTO prospectos_propiedades (prospecto_id, propiedad_id, fecha_interes)
                VALUES (:prospecto_id, :propiedad_id, NOW())
            ");
            $stmtRelacion->execute([
                ':prospecto_id' => $prospecto_id,
                ':propiedad_id' => $propiedad_id
            ]);


            // Redirige al listado de clientes interesados
            header("Location: ../clientes_interesados.php?msg=cliente_registrado");
            exit();
        } catch (PDOException $e) {
            echo "Error al registrar cliente: " . $e->getMessage();
        }
    } else {
        echo "Por favor completa todos los campos.";
    }
}
?>
