<?php
session_start();
if (empty($_SESSION["id"]) || !is_numeric($_SESSION["id"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["accion"])) {

    // CREAR SOLICITUD
    if ($_POST["accion"] === "crear") {
        $encabezado  = trim($_POST["titulo"]      ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $prioridad   = "Sin Asignar";
        $id_us       = $_SESSION["id"];

        $errores = [];
        if (empty($encabezado)) {
            $errores[] = "El título es obligatorio.";
        }
        if (strlen($descripcion) <= 10) {
            $errores[] = "La descripción debe tener más de 10 caracteres.";
        }
        if (!empty($errores)) {
            $_SESSION["error"] = implode(" | ", $errores);
            header("Location: ../Solicitante.php");
            exit();
        }

        $stmt = $conexion->prepare(
            "INSERT INTO solicitud (id_us, encabezado, descripcion, prioridad)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isss", $id_us, $encabezado, $descripcion, $prioridad);
        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION["exito"] = "Solicitud enviada correctamente.";
        } else {
            $errorMsg = $stmt->error;
            $stmt->close();
            $_SESSION["error"] = "Error al enviar la solicitud: $errorMsg";
        }
        header("Location: ../Solicitante.php");
        exit();
    }

    // APROBAR SOLICITUD
    if ($_POST["accion"] === "aprobar") {
        $id_sol = (int)($_POST["id_sol"] ?? 0);
        $id_us  = $_SESSION["id"];

        $stmtCheck = $conexion->prepare(
            "SELECT id_sol FROM solicitud WHERE id_sol = ? AND id_us = ? AND id_estado = 4"
        );
        $stmtCheck->bind_param("ii", $id_sol, $id_us);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows === 0) {
            $stmtCheck->close();
            $_SESSION["error"] = "No se puede aprobar esta solicitud.";
            header("Location: ../Solicitante.php");
            exit();
        }
        $stmtCheck->close();

        $stmtBit = $conexion->prepare(
            "UPDATE bitacora SET aprobado = true WHERE id_sol = ?"
        );
        $stmtBit->bind_param("i", $id_sol);
        $stmtBit->execute();
        $stmtBit->close();

        $stmtAsg = $conexion->prepare(
            "UPDATE asignacion SET estado_asignacion = 'completada', fecha_fin = NOW()
             WHERE id_sol = ? AND estado_asignacion = 'activa'"
        );
        $stmtAsg->bind_param("i", $id_sol);
        $stmtAsg->execute();
        $stmtAsg->close();

        $_SESSION["exito"] = "Solicitud aprobada y finalizada.";
        header("Location: ../Solicitante.php");
        exit();
    }
}

header("Location: ../Solicitante.php");
exit();
?>