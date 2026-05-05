<?php
    // Configuración del servidor de base de datos PostgreSQL
    $host = "localhost";  // Dirección del servidor (localhost para desarrollo local)
    $port = "5432";       // Puerto por defecto de PostgreSQL
    $dbname = "lorent_inmobiliaria_dos";  // Nombre de la base de datos
    $user = "postgres";   // Usuario de PostgreSQL (cambiar según tu configuración)
    $password = "post77";  // Contraseña del usuario (cambiar según tu configuración)

    // Cadena de conexión DSN (Data Source Name) para PDO
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";

    try {
        // Crea una nueva conexión PDO usando el DSN
        $conexion = new PDO($dsn);
        // Configura PDO para lanzar excepciones en caso de error
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // (Opcional) Mensaje de confirmación de conexión exitosa (comentado para producción)
        // echo "Conectado correctamente a PostgreSQL";
    } catch (PDOException $e) {
        // Si hay error en la conexión, termina el script y muestra el mensaje de error
        die("Error de conexión: " . $e->getMessage());
    }
?>