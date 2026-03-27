<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — ITSRV Soporte Técnico</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="pagina-login">

    <div class="login-contenedor">  <!-- Limita ancho a 420px -->
        <div class="login-tarjeta">

            <?php 
                include "php/conexion.php";
                include "php/controlador_login.php";
            ?>

            <div class="login-logo">
                <!-- Logo institucional -->
                <div class="marca-emblema"><img src="img/logo_tec_.png" alt="logo del Instituto Tecnológico Superior de Rioverde" width="48"></div>
                <div>
                    <div class="login-logo-nombre">ITSRV</div>
                    <div class="login-logo-subtitulo">Instituto Tecnológico Superior de Rioverde</div>
                </div>
            </div>
            <!-- Titulo y subtitulo de bienvenida -->
            <div class="login-titulo">Bienvenido</div>
            <div class="login-subtitulo">Ingresa tus credenciales para acceder al sistema.</div>

            <form action="" method="post"> <!-- envía al mismo archivo php-->
                <!-- Campo: username -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="username">Usuario</label>
                    <div class="campo-c">
                        <input class="campo-form" type="text" id="username" name="username" placeholder="Nombre de usuario" required>
                    </div>
                </div>
                <!-- campo: password -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="password">Contraseña</label>
                    <div class="campo-c">
                        <input class="campo-form" type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" id="togglePass" class="btn-mostrar-contrasena">👁</button>
                    </div>
                </div>

                <input type="submit" class="btn-iniciar-sesion" name="btn-iniciar-sesion" value="Iniciar Sesión">
            </form>

        </div>

        <div class="login-pie">
            Sistema de Gestión de Soporte Técnico --- MAKASU Y ASOCIADOS ITSRV © 2025
        </div>
    </div>

    <script src="js/inicio-sesion.js"></script>
</body>
</html>