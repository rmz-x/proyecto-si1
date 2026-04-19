<?php 
    session_start();
    session_destroy();
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

    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="asscet/css/estilos.css">
</head>
<body>
    
    <main>

        <div class="contenedor_todo">

            <div class="caja_trasera">

                <div class="caja_trasera_login">
                    <h3> ¿Ya tienes cuenta?</h3>
                    <p> Inicia sesión para entrar en la pagina </p>
                    <button id="btn_iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja_trasera_register">
                    <h3> ¿Aún no tienes una cuenta?</h3>
                    <p> Registrate para que puedas iniciar sesión </p>
                    <button id="btn_registrarse">Registrarse</button>
                </div>
            </div>
            <!--formulario de login y registro-->
            <div class="contenedor_login-register">
                <!--login-->
                <form action="php/login_usuario_be.php" method="POST" class="formulario_login">

                    <h2>Iniciar Sesión</h2>
                    <input type="text" placeholder="Correo Electronico" name="correo"> 
                    <input type="password" placeholder="Contraseña" name="contrasena"> 
                    <button>Entrar</button>
                </form>
                <!--registro-->
                <form action="php/registro_usuario_be.php" method="POST" class="formulario_register">
                    <h2>Registrarse</h2>
                    <input type="text" placeholder="Nombre Completo" name="nombre">
                    <input type="text" placeholder="Correo Electronico" name="correo">
                    <input type="text" placeholder="Usuario" name="usuario">
                    <input type="password" placeholder="Contraseña" name="contrasena">
                    <button>Registrarse</button>
                </form>
            </div>
        </div>

    </main>
    <script src="asscet/js/validaciones.js"></script>
    <script src="asscet/js/scrip.js"></script>
</body>
</html>