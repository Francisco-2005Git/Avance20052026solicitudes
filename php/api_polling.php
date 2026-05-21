<?php
session_start();
if (empty($_SESSION['id']) || !is_numeric($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'no-session']);
    exit;
}

require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

$id  = (int)$_SESSION['id'];
$rol = (int)$_SESSION['id_rol'];
$desdeNotif = max(0, (int)($_GET['desde_notif'] ?? 0));

$resp = [];

// ── NOTIFICACIONES ────────────────────────────────────────────────────────────
$row = $conexion->query(
    "SELECT COALESCE(MAX(id_not), 0) AS mx, COUNT(*) AS cnt
     FROM notificacion WHERE id_us = $id"
)->fetch_object();
$resp['notif_max_id'] = (int)$row->mx;
$resp['notif_count']  = (int)$row->cnt;

if ($desdeNotif > 0 && $resp['notif_max_id'] > $desdeNotif) {
    $r2 = $conexion->query(
        "SELECT id_not, mensaje,
                DATE_FORMAT(fecha_envio,'%d/%m/%Y %H:%i') AS fecha_fmt
         FROM notificacion
         WHERE id_us = $id AND id_not > $desdeNotif
         ORDER BY fecha_envio ASC"
    );
    $resp['notificaciones_nuevas'] = $r2->fetch_all(MYSQLI_ASSOC);
}

// ── DATOS POR ROL ─────────────────────────────────────────────────────────────
if ($rol === 2) { // Trabajador
    $row = $conexion->query(
        "SELECT COALESCE(MAX(id_sol), 0) AS mx, COUNT(*) AS cnt
         FROM solicitud WHERE id_estado = 1"
    )->fetch_object();
    $resp['sol_max_id'] = (int)$row->mx;
    $resp['sol_count']  = (int)$row->cnt;

    $row2 = $conexion->query(
        "SELECT GROUP_CONCAT(
             CONCAT(a.id_asg,':',a.estado_asignacion,':',s.id_estado)
             ORDER BY a.id_asg
         ) AS fp
         FROM asignacion a
         JOIN solicitud s ON s.id_sol = a.id_sol
         WHERE a.id_trabajador = $id AND a.estado_asignacion != 'cancelada'"
    )->fetch_object();
    $resp['asg_fingerprint'] = md5($row2->fp ?? '');
}

if ($rol === 1) { // Solicitante
    $row = $conexion->query(
        "SELECT GROUP_CONCAT(CONCAT(id_sol,':',id_estado) ORDER BY id_sol) AS fp,
                COUNT(*) AS cnt
         FROM solicitud WHERE id_us = $id"
    )->fetch_object();
    $resp['sol_fingerprint'] = md5($row->fp ?? '');
    $resp['sol_count']       = (int)$row->cnt;
}

echo json_encode($resp);
