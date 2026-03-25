<?php
session_start();
if (!empty($_POST["btn-iniciar-sesion"])) {
    if (!empty($_POST["username"]) and !empty($_POST["password"])) {
        $usuario = $_POST["username"];
        $password = $_POST["password"];
        $sql = $conexion->query("SELECT * FROM usuario WHERE username='$usuario' and contrasena='$password'");
        
        if ($datos=$sql->fetch_object()) {
            $_SESSION["id"]=$datos->id_us;
            $_SESSION["nombre"]=$datos->nombre;
            $_SESSION["apellidoP"]=$datos->apellidoP;
            header("location: Administrador.php");
        } else {
            echo "<div>Acceso denegado</div>";
        }

    } else {
        echo "Campos vacíos";
    }

}

?>