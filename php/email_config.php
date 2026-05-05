<?php
// Configuración SMTP para enviar correos de recuperación.
// Cambia estos valores por tu cuenta real de Gmail y por la contraseña de aplicación.
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'smtp_auth' => true,
    'smtp_secure' => 'tls',
    'username' => 'tuemail@gmail.com',
    'password' => 'tu_contraseña_app',
    'from_email' => 'tuemail@gmail.com',
    'from_name' => 'Lorent Inmobiliaria',
];
