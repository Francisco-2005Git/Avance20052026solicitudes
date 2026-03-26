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

        // Variables para todos los campos
        $nombre     = trim($_POST["nombre"]     ?? "");
        $app        = trim($_POST["app"]        ?? "");
        $apm        = trim($_POST["apm"]        ?? "") ?: null; // Opcional, NULL si está vacío.
        $username   = trim($_POST["username"]   ?? "");
        $password   = $_POST["password"]        ?? "";
        $password2  = $_POST["password2"]        ?? "";
        $id_rol     = (int)($_POST["id_rol"]    ?? 0);
        $id_area    = (int)($_POST["id_area"]   ?? 0);
        $disponible = isset($_POST["disponible"]) ? (int)$_POST["disponible"] : 1;

        // Validaciones
        $errores = [];


    }

}

?>