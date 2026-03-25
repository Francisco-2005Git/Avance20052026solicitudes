<?php

if (!empty($_POST["btn-iniciar-sesion"])) {
    if (!empty($_POST["username"]) and !empty($_POST["password"])) {
        $usuario = $_POST["username"];
        $password = $_POST["password"];
        $sql = $conexion->query("SELECT * FROM usuario WHERE username='$usuario' and contrasena='$password'");
        
        if ($datos=$sql->fetch_object()) {
            header("location: inicio.php");
        } else {
            echo "<div>Acceso denegado</div>";
        }

    } else {
        echo "Campos vacíos";
    }

}

?>