<?php
// Inicia la sesión (aunque no se use mucho aquí, por consistencia)
session_start();
// Incluye la conexión a la base de datos
include 'php/conexion_be.php';
// Importa las clases de PHPMailer para enviar emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluye los archivos necesarios de PHPMailer
require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

// Variable para almacenar mensajes de error o éxito
$message = '';
// Variable para saber si el código fue generado exitosamente
$codigo_generado = false;
$codigo_id = null;

// Verifica si se envió el formulario por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia el correo del formulario
    $correo = trim($_POST['correo']);

    // Verifica si el usuario existe en la base de datos
    $stmt = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Si no existe, muestra mensaje de error
        $message = 'No se encontró un usuario con ese correo.';
    } else {
        // Genera un código aleatorio de 6 dígitos (rellenado con ceros a la izquierda)
        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Calcula la expiración del código (15 minutos desde ahora)
        $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Guarda el código en la base de datos
        $stmtInsert = $conexion->prepare("INSERT INTO codigos_recuperacion (usuario_id, codigo, expiracion) VALUES (?, ?, ?)");
        $stmtInsert->execute([$user['id'], $codigo, $expiracion]);
        $codigo_id = $conexion->lastInsertId();

        // En localhost: mostrar el código en pantalla en lugar de enviar email
        // En producción, descomentar el código de PHPMailer arriba
        $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = '4962d2024fffb3';
                $mail->Password = 'c24995f59a1583';

                $mail->setFrom('no-reply@lorent.com', 'Lorent Inmobiliaria');
                $mail->addAddress($correo, $user['nombre']);
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body = "Hola {$user['nombre']},\n\nTu codigo de recuperacion es: $codigo\nEste codigo expira en 15 minutos.\n\nEquipo Lorent";

                $mail->send();
                $message = "✅ Se envió un correo con tu código de recuperación.";
                $codigo_generado = true;
            } catch (Exception $e) {
                $message = "❌ Error al enviar el correo: {$mail->ErrorInfo}";
            }

        
        // Variable para indicar que se generó el código exitosamente
        $codigo_generado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña — Lorent Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asscet/css/estilos.css">
    <style>
        .recovery-form{max-width:400px;margin:50px auto;padding:20px;border:1px solid #ddd;border-radius:8px;background:#fff}
        .recovery-form h2{text-align:center;margin-bottom:20px}
        .form-group{margin-bottom:15px}
        .form-group label{display:block;margin-bottom:5px}
        .form-group input{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px}
        .btn-submit{width:100%;padding:10px;background:#007bff;color:#fff;border:none;border-radius:4px;cursor:pointer}
        .btn-submit:hover{background:#0056b3}
        .message{text-align:center;margin-top:10px;color:red}
    </style>
</head>
<body>
    <div class="recovery-form">
        <h2>Recuperar Contraseña</h2>
        <form method="POST">
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <button type="submit" class="btn-submit">Enviar Código</button>
        </form>
        <p class="message" style="color: <?php echo (isset($codigo_generado) && $codigo_generado) ? '#28a745' : '#dc3545'; ?>; border: 1px solid <?php echo (isset($codigo_generado) && $codigo_generado) ? '#d4edda' : '#f8d7da'; ?>; background: <?php echo (isset($codigo_generado) && $codigo_generado) ? '#d4edda' : '#f8d7da'; ?>; padding: 10px; border-radius: 4px;"><?php echo $message; ?></p>
        
        <?php if (isset($codigo_generado) && $codigo_generado): ?>
            <form method="GET" action="verificar_codigo.php" style="text-align: center; margin-top: 15px;">
                <input type="hidden" name="codigo_id" value="<?php echo htmlspecialchars($codigo_id); ?>">
                <button type="submit" class="btn-submit" style="background:#28a745">Continuar a Verificación</button>
            </form>
        <?php endif; ?>
        
        <!-- <p style="text-align:center;margin-top:20px"><a href="index.php">Volver al Login</a></p> -->

        <p style="text-align:center;margin-top:20px">
            <a href="index.php" class="btn-volver">Volver al Login</a>
        </p>



    </div>
</body>
</html>