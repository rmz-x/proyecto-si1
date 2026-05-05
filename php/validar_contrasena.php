<?php
/**
 * Valida que una contraseña cumpla con los criterios de seguridad requeridos
 * 
 * Criterios:
 * - Mínimo 8 caracteres
 * - Al menos una letra mayúscula
 * - Al menos una letra minúscula
 * - Al menos un número
 * 
 * @param string $contrasena La contraseña a validar
 * @return array Array con 'valida' (bool) y 'errores' (array de mensajes)
 */
function validarContrasena($contrasena) {
    $errores = [];
    
    // Verificar longitud mínima
    if (strlen($contrasena) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    
    // Verificar que contenga al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $contrasena)) {
        $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
    }
    
    // Verificar que contenga al menos una letra minúscula
    if (!preg_match('/[a-z]/', $contrasena)) {
        $errores[] = "La contraseña debe contener al menos una letra minúscula.";
    }
    
    // Verificar que contenga al menos un número
    if (!preg_match('/[0-9]/', $contrasena)) {
        $errores[] = "La contraseña debe contener al menos un número.";
    }
    
    return [
        'valida' => count($errores) === 0,
        'errores' => $errores
    ];
}
?>
