<?php
// Inicia la sesión para manejar datos del usuario durante su visita
session_start();
// Destruye cualquier sesión existente para asegurar un login limpio
session_destroy();
// Si hay un usuario logueado, redirige a la página de bienvenida
if(isset($_SESSION['usuario'])){
    header("location: bienvenida.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>

    <!-- Enlace a fuentes de Google para el estilo de texto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Enlace al archivo CSS principal para estilos -->
    <link rel="stylesheet" href="asscet/css/estilos.css">

</head>
<body>

    <main>

        <!-- Contenedor principal de toda la página -->
        <div class="contenedor_todo">

            <!-- Caja trasera con animaciones para login y registro -->
            <div class="caja_trasera">

                <!-- Sección para usuarios que ya tienen cuenta -->
                <div class="caja_trasera_login">
                    <h3> ¿Ya tienes cuenta?</h3>
                    <p> Inicia sesión para entrar en la pagina </p>
                    <button id="btn_iniciar-sesion">Iniciar Sesión</button>
                </div>

                <!-- Sección para usuarios nuevos -->
                <div class="caja_trasera_register">
                    <h3> ¿Aún no tienes una cuenta?</h3>
                    <p> Registrate para que puedas iniciar sesión </p>
                    <button id="btn_registrarse">Registrarse</button>
                </div>
            </div>

            <!-- Contenedor de los formularios de login y registro -->
            <div class="contenedor_login-register">

                <!-- Formulario de login: envía datos a login_usuario_be.php -->
                <form action="php/login_usuario_be.php" method="POST" class="formulario_login">

                    <h2>Iniciar Sesión</h2>
                    <!-- Campo para el correo electrónico -->
                    <input type="text" placeholder="Correo Electronico" name="correo">
                    <!-- Campo para la contraseña con toggle -->
                    <div class="password-wrapper">
                        <input type="password" placeholder="Contraseña" name="contrasena">
                        <button class="password-toggle-btn" title="Mostrar contraseña">👁️</button>
                    </div>
                    <!-- Botón para enviar el formulario -->
                    <button>Entrar</button>
                    <!-- Enlace para recuperar contraseña si se olvida -->
                    <p style="text-align:center;margin-top:10px"><a href="recuperar_contrasena.php" style="color:#007bff;text-decoration:none">¿Olvidaste tu contraseña?</a></p>
                </form>
                <!-- Formulario de registro: envía datos a registro_usuario_be.php -->
                <form action="php/registro_usuario_be.php" method="POST" class="formulario_register">
                    <h2>Registrarse</h2>
                    <!-- Campo para el nombre completo del usuario -->
                    <input type="text" placeholder="Nombre Completo" name="nombre">
                    <!-- Campo para el correo electrónico -->
                    <input type="text" placeholder="Correo Electronico" name="correo">
                    <!-- Campo para el nombre de usuario -->
                    <input type="text" placeholder="Usuario" name="usuario">
                    <!-- Campo para la contraseña con toggle -->
                    <div class="password-wrapper">
                        <input type="password" placeholder="Contraseña" name="contrasena">

                    <button class="password-toggle-btn" title="Mostrar contraseña">👁️</button>

                    </div>
                    <!-- Botón para enviar el formulario de registro -->
                    <button>Registrarse</button>
                </form>
            </div>
        </div>

    </main>

    <!-- Script para validaciones de formularios -->
    <script src="asscet/js/validaciones.js"></script>
    <!-- Script principal para animaciones y lógica del login/registro -->
    <script src="asscet/js/scrip.js"></script>
</body>
</html>