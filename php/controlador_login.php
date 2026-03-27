<?php
session_start();
if (!empty($_POST["btn-iniciar-sesion"])) {
    if (!empty($_POST["username"]) and !empty($_POST["password"])) {
        
        // Definición de variables con consulta a la BD
        $usuario = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        // Consulta con sentencia pre-hecha
        $statement = $conexion->prepare("SELECT id_us, nombre, app, contrasena FROM usuario WHERE username= ?");
        $statement->bind_param("s", $usuario);
        $statement->execute();
        $resultado = $statement->get_result();

        if ($datos = $resultado->fetch_object()) {

            // Verificación del hash para regenerar la ID de la sesión
            if (password_verify($password, $datos->contrasena)) {
                session_regenerate_id(true);

                $_SESSION["id"]        =$datos->id_us;
                $_SESSION["nombre"]    =$datos->nombre;
                $_SESSION["app"]       =$datos->app;

                header("location: Administrador.php");
                exit();
            } else {
                echo "<div class='alerta alerta-error'>Usuario o contraseña incorrectos</div>";
            }
        } else {
            echo "<div class='alerta alerta-error'>Usuario o contraseña incorrectos</div>";
        }

        $statement->close();

    } else {
        echo "<div>Campos vacíos</div>";
    }
}
?>