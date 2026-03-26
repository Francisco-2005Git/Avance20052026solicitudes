<?php
session_start();

// Comprobación para darle acceso solo al Administrador.
if (empty($_SESSION["id"]) || !is_numeric($_SESSION["id"])) {
    header("Location: ../login.php");
    exit();
}

// Conexión
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["accion"])) {
    if ($_POST["accion"] === "agregar") {

        // 1.- Variables para todos los campos
        $nombre     = trim($_POST["nombre"]     ?? "");
        $app        = trim($_POST["app"]        ?? "");
        $apm        = trim($_POST["apm"]        ?? "") ?: null; // Opcional, NULL si está vacío.
        $username   = trim($_POST["username"]   ?? "");
        $password   = $_POST["password"]        ?? "";
        $password2  = $_POST["password2"]       ?? "";
        $id_rol     = (int)($_POST["id_rol"]    ?? 0);
        $id_area    = (int)($_POST["id_area"]   ?? 0);
        $disponible = isset($_POST["disponible"]) ? (int)$_POST["disponible"] : 1;

        // 2.- Validaciones
        $errores = [];

        if (empty($nombre) || empty($app) || empty($username)) {
            $errores[] = "Los campos 'Nombre', 'Apellido Paterno' y 'Usuario/Username' son obligatorios.";
        }

        if (strlen($password) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        }

        if ($password !== $password2) {
            $errores[] = "Las contraseñas no coinciden.";
        }

        if (!in_array($id_rol, [1, 2])) {
            $errores[] = "Rol no válido.";
        }

        if ($id_area < 1) {
            $errores[] = "Es necesario seleccionar por lo menos un área.";
        }

        if (!empty($errores)) {
            // Regresa los errores y los muestra en un mensaje
            $msg = implode(" | ", $errores);
            header("Location: ../Administrador.php?error=" . urlencode($msg));
            exit();
        }

        // 3. Verificar que el username no exista en la BD
        $statementCheck = $conexion->prepare(
            "SELECT id_us FROM usuario WHERE username = ?"
        );
        $statementCheck->bind_param("s", $username);
        $statementCheck->execute();
        $statementCheck->store_result();

        if ($statementCheck->num_rows > 0) {
            $statementCheck->close();
            header("Location: ../Administrador.php?error=" . urlencode("El nombre de usuario ya existe."));
            exit();
        }
        $statementCheck->close();
        
        // 4. Hasheo de contraseña e inserción
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $statement = $conexion->prepare(
            "INSERT INTO usuario (nombre, app, apm, username, contrasena, id_rol, disponible, id_area)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $statement->bind_param(
            "sssssiii",
            $nombre, $app, $apm, $username,
            $hash, $id_rol, $disponible, $id_area
        );

        if ($statement->execute()) {
            $statement->close();
            $_SESSION["exito"] = "Usuario registrado correctamente.";
        } else {
            $errorMsg = $statement->error;
            $statement->close();
            $_SESSION["error"] = "Error al guardar: $errorMsg";
        }
        $_SESSION["seccion_activa"] = "admin-usuarios";
        header("Location: ../Administrador.php");
        exit();
    }
}

// Si de algún modo se llega aquí sin un POST correcto, salir.
header("Location: ../Administrador.php");
exit();
?>