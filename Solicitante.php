<!-- PHP -->
<!-- Comprobación de sesión -->
<?php
session_start();
if (empty($_SESSION["id"]) || !is_numeric($_SESSION["id"]) || $_SESSION["id_rol"] != 1) {
    header("Location: index.php");
    exit();
}

// Mensajes flash
$msgExito = $_SESSION["exito"] ?? null;
$msgError = $_SESSION["error"] ?? null;
unset($_SESSION["exito"], $_SESSION["error"]);

require_once "php/conexion.php";

$stmtSolicitudes = $conexion->prepare(
    "SELECT s.id_sol, s.encabezado, s.prioridad, s.fecha_creacion, s.fecha_limite, s.id_estado,
            e.nombre AS estado, ar.nombre AS area,
            a.fecha_fin AS fecha_fin_asignacion,
            b.id_bit, b.evidencia
     FROM solicitud s
     JOIN estado_solicitud e ON s.id_estado = e.id_estado
     JOIN area ar ON s.id_area = ar.id_area
     LEFT JOIN asignacion a ON a.id_sol = s.id_sol AND a.estado_asignacion = 'activa'
     LEFT JOIN bitacora b ON b.id_sol = s.id_sol
         AND b.id_bit = (SELECT MAX(id_bit) FROM bitacora WHERE id_sol = s.id_sol)
     WHERE s.id_us = ?
     ORDER BY s.fecha_creacion DESC"
);
$stmtSolicitudes->bind_param("i", $_SESSION["id"]);
$stmtSolicitudes->execute();
$solicitudes = $stmtSolicitudes->get_result();
$totalSolicitudes = $solicitudes->num_rows;
$stmtSolicitudes->close();
$listaActivas     = [];
$listaCompletadas = [];
while ($s = $solicitudes->fetch_object()) {
    if (strtolower($s->estado) === 'finalizada') {
        $listaCompletadas[] = $s;
    } else {
        $listaActivas[] = $s;
    }
}

// Trae las asignaciones activas y completadas del trabajador
$stmtAsignaciones = $conexion->prepare(
    "SELECT a.id_asg, a.estado_asignacion, a.fecha_inicio, a.fecha_fin,
            s.encabezado, s.prioridad,
            ar.nombre AS area
     FROM asignacion a
     JOIN solicitud s ON a.id_sol = s.id_sol
     JOIN area ar ON s.id_area = ar.id_area
     WHERE a.id_trabajador = ?
       AND a.estado_asignacion != 'cancelada'
     ORDER BY a.estado_asignacion ASC, a.fecha_inicio DESC"
);
$stmtAsignaciones->bind_param("i", $_SESSION["id"]);
$stmtAsignaciones->execute();
$asignaciones = $stmtAsignaciones->get_result();
$totalAsignaciones = $asignaciones->num_rows;
$stmtAsignaciones->close();
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Solicitudes — Usuario | ITSRV</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="layout-app">

    <aside class="sidebar">
        <div class="sidebar-marca">
            <div class="marca-fila">
                <div class="marca-emblema"><img src="img/logo_tec_.png" alt="logo del Instituto Tecnológico Superior de Rioverde" width="30"></div>
                <div>
                    <div class="marca-nombre">ITSRV</div>
                    <div class="marca-subtitulo">SOPORTEC</div>
                </div>
            </div>
            <div class="usuario-pastilla">
                <!-- Iniciales calculadas desde la sesión ej. Juan Perez= JP -->
                <div class="usuario-avatar">
                    <?php echo strtoupper(substr($_SESSION["nombre"], 0, 1) . substr($_SESSION["app"], 0, 1)); ?>
                </div>
                <div>
                    <div class="usuario-nombre">
                        <?php echo htmlspecialchars($_SESSION["nombre"] . " " . $_SESSION["app"]); ?>
                    </div>
                    <div class="usuario-rol">Solicitante</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-etiqueta-seccion">Solicitudes</div>
            <a href="#" class="nav-link nav-item active" data-section="crear">Nueva Solicitud</a>
            <a href="#" class="nav-link nav-item" data-section="creadas">
                Mis Solicitudes
                <?php if (count($listaActivas) > 0): ?>
                    <span class="nav-contador"><?= count($listaActivas) ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="nav-link nav-item" data-section="notificaciones">Notificaciones</a>
        </nav>

        <div class="sidebar-pie">
            <a href="php/controlador_cerrar.php" class="btn-cerrar-sesion">
                <span>❌</span> Cerrar Sesión
            </a>
        </div>
    </aside>

    <div class="contenido-principal">

        <header class="topbar">
            <div>
                <div class="topbar-titulo" id="topbar-titulo">Nueva Solicitud</div>
                <div class="topbar-subtitulo">Instituto Tecnológico Superior de Rioverde</div>
            </div>
        </header>

        <div class="cuerpo-pagina">
            <?php if ($msgExito): ?>
                <div class="alerta alerta-exito"><?= htmlspecialchars($msgExito) ?></div>
            <?php endif; ?>
            <?php if ($msgError): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($msgError) ?></div>
            <?php endif; ?>

            <!--  NUEVA SOLICITUD  -->
            <div id="crear" class="section active">
                <div class="tarjeta" style="max-width: 680px;">
                    <div class="tarjeta-encabezado">
                        <div class="tarjeta-titulo">Nueva solicitud de soporte</div>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <!-- Utiliza el método POST para enviar a controlador_solicitud.php por POST -->
                        <form action="php/controlador_solicitud.php" method="POST">

                            <div class="grupo-form">
                                <label class="etiqueta-form" for="titulo">Título de la solicitud</label>
                                <input class="campo-form" type="text" id="titulo" name="titulo"
                                    placeholder="Ej: Equipo sin acceso a red" required>
                            </div>

                            <!--  red (grid) con dos columnas. Una  para Área y otra para Prioridad -->
                            <div class="fila-form">
                                <div class="grupo-form">
                                    <label class="etiqueta-form" for="area">Área</label>
                                    <select class="campo-form" id="area" name="id_area" required>
                                        <option value="">— Seleccionar área —</option>
                                        <optgroup label="Dirección">
                                            <option value="1">Dirección General</option>
                                            <option value="2">Dirección Académica</option>
                                            <option value="3">Dirección de Vinculación</option>
                                        </optgroup>
                                        <optgroup label="Académico">
                                            <option value="4">Docencia</option>
                                            <option value="5">Desarrollo Académico</option>
                                            <option value="6">Coordinación de Inglés</option>
                                            <option value="7">Biblioteca</option>
                                            <option value="8">Titulación</option>
                                            <option value="9">Psicopedagogía</option>
                                            <option value="10">Cultura y Deportes</option>
                                        </optgroup>
                                        <optgroup label="Administrativo">
                                            <option value="11">Recursos Materiales</option>
                                            <option value="12">Recursos Financieros</option>
                                            <option value="13">Caja</option>
                                            <option value="14">Planeación</option>
                                            <option value="15">Calidad</option>
                                            <option value="16">Transparencia</option>
                                            <option value="17">Centro de Copiado</option>
                                        </optgroup>
                                        <optgroup label="Jefaturas">
                                            <option value="18">Industrial</option>
                                            <option value="19">Innovación Agrícola</option>
                                            <option value="20">Informática</option>
                                            <option value="21">Sistemas Computacionales</option>
                                            <option value="22">Gestión Empresarial</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="grupo-form">
                                <label class="etiqueta-form" for="descripcion">Descripción detallada</label>
                                <textarea class="campo-form" id="descripcion" name="descripcion"
                                        rows="5" placeholder="Describe el problema con el mayor detalle posible" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primario w-full"
                                    style="justify-content:center; padding:10px;">
                                Enviar Solicitud
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MIS SOLICITUDES -->
            <div id="creadas" class="section" style="display:none;">
                <div class="tarjeta">
                    <div class="tarjeta-encabezado">
                        <?php if ($totalSolicitudes > 0): ?>
                            <div class="tarjeta-titulo">
                                Mis Solicitudes
                            </div>
                        <?php endif; ?>
                        <button class="btn btn-primario btn-pequeno" onclick="navTo('crear')">Nueva solicitud</button>
                    </div>

                    <?php if ($totalSolicitudes === 0): ?>
                        <div class="tarjeta-cuerpo">
                            <p style="color:#8f98b2; text-align:center;">No tienes solicitudes registradas.</p>
                        </div>
                    <?php else: ?>

                        <!-- ACTIVAS -->
                        <?php if (!empty($listaActivas)): ?>
                            <div style="padding: 12px 16px 4px;">
                                <p style="font-size:11px; font-weight:700; color:#8f98b2; text-transform:uppercase; letter-spacing:1px;">
                                    Activas
                                </p>
                            </div>
                            <div class="contenedor-tabla">
                                <table style="table-layout:fixed; width:100%">
                                        <colgroup>
                                            <col style="width:6%">
                                            <col style="width:28%">
                                            <col style="width:15%">
                                            <col style="width:10%">
                                            <col style="width:10%">
                                            <col style="width:10%">
                                            <col style="width:12%">
                                            <col style="width:9%">
                                        </colgroup>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Área</th>
                                            <th>Prioridad</th>
                                            <th>Fecha</th>
                                            <th>Fecha límite</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($listaActivas as $s): ?>
                                            <?php
                                                $clasePrioridad = match(strtolower($s->prioridad)) {
                                                    'alta'  => 'etiqueta-alta',
                                                    'media' => 'etiqueta-media',
                                                    'baja'  => 'etiqueta-baja',
                                                    default => ''
                                                };
                                                $claseEstado = match($s->id_estado) {
                                                    1 => 'etiqueta-pendiente',
                                                    2 => 'etiqueta-proceso',
                                                    4 => 'etiqueta-pendiente',
                                                    5 => 'etiqueta-cancelada',
                                                    default => ''
                                                };
                                                $puntoEstado = match($s->id_estado) {
                                                    1 => 'pendiente',
                                                    2 => 'proceso',
                                                    default => ''
                                                };
                                                $fecha = date("d/m/Y H:i:s", strtotime($s->fecha_creacion));
                                                $fechalimite = $s->fecha_fin_asignacion
                                                    ? date("d/m/Y H:i:s", strtotime($s->fecha_fin_asignacion))
                                                    : date("d/m/Y H:i:s", strtotime($s->fecha_limite));
                                                $pdfPath = null;
                                                if ($s->id_estado == 4 && $s->evidencia) {
                                                    $carpeta = dirname(explode(',', $s->evidencia)[0]);
                                                    $pdfPath = $carpeta . '/reporte_' . $s->id_bit . '.pdf';
                                                }
                                            ?>
                                            <tr>
                                                <td><span class="texto-apagado texto-xs">#<?= $s->id_sol ?></span></td>
                                                <td><strong><?= htmlspecialchars($s->encabezado) ?></strong></td>
                                                <td><?= htmlspecialchars($s->area) ?></td>
                                                <td><span class="etiqueta <?= $clasePrioridad ?>"><?= htmlspecialchars($s->prioridad) ?></span></td>
                                                <td class="texto-apagado"><?= $fecha ?></td>
                                                <td><?= $fechalimite ?></td>
                                                <td>
                                                    <span class="etiqueta <?= $claseEstado ?>">
                                                        <span class="punto-estado-solicitud <?= $puntoEstado ?>"></span>
                                                        <?= htmlspecialchars($s->estado) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($s->id_estado == 4): ?>
                                                        <div style="display:flex; gap:4px; flex-wrap:wrap; align-items:center;">
                                                            <?php if ($pdfPath): ?>
                                                                <a href="<?= htmlspecialchars($pdfPath) ?>" target="_blank"
                                                                   class="btn btn-fantasma btn-pequeno">Ver PDF</a>
                                                            <?php endif; ?>
                                                            <form action="php/controlador_solicitud.php" method="POST" style="margin:0;">
                                                                <input type="hidden" name="accion" value="aprobar">
                                                                <input type="hidden" name="id_sol" value="<?= $s->id_sol ?>">
                                                                <button type="submit" class="btn btn-exito btn-pequeno">Aprobar</button>
                                                            </form>
                                                            <button class="btn btn-peligro btn-pequeno"
                                                                    onclick="abrirModalRechazo(<?= $s->id_sol ?>)">Rechazar</button>
                                                        </div>
                                                    <?php elseif ($s->id_estado == 5): ?>
                                                        <span class="texto-apagado texto-xs">Esperando reenvío...</span>
                                                    <?php else: ?>
                                                        <span class="texto-apagado texto-xs">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- COMPLETADAS -->
                        <?php if (!empty($listaCompletadas)): ?>
                            <div style="padding: 12px 16px 4px; margin-top: 8px;">
                                <p style="font-size:11px; font-weight:700; color:#8f98b2; text-transform:uppercase; letter-spacing:1px;">
                                    Completadas
                                </p>
                            </div>
                            <div class="contenedor-tabla">
                                <table style="table-layout:fixed; width:100%">
                                        <colgroup>
                                            <col style="width:6%">
                                            <col style="width:28%">
                                            <col style="width:15%">
                                            <col style="width:10%">
                                            <col style="width:10%">
                                            <col style="width:10%">
                                            <col style="width:12%">
                                            <col style="width:9%">
                                        </colgroup>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Área</th>
                                            <th>Prioridad</th>
                                            <th>Fecha</th>
                                            <th>Fecha límite</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($listaCompletadas as $s): ?>
                                            <?php
                                                $clasePrioridad = match(strtolower($s->prioridad)) {
                                                    'alta'  => 'etiqueta-alta',
                                                    'media' => 'etiqueta-media',
                                                    'baja'  => 'etiqueta-baja',
                                                    default => ''
                                                };
                                                $fecha = date("d/m/Y H:i:s", strtotime($s->fecha_creacion));
                                                $fechalimite = $s->fecha_fin_asignacion
                                                    ? date("d/m/Y H:i:s", strtotime($s->fecha_fin_asignacion))
                                                    : date("d/m/Y H:i:s", strtotime($s->fecha_limite));
                                            ?>
                                            <tr>
                                                <td><span class="texto-apagado texto-xs">#<?= $s->id_sol ?></span></td>
                                                <td><strong><?= htmlspecialchars($s->encabezado) ?></strong></td>
                                                <td><?= htmlspecialchars($s->area) ?></td>
                                                <td><span class="etiqueta <?= $clasePrioridad ?>"><?= htmlspecialchars($s->prioridad) ?></span></td>
                                                <td class="texto-apagado"><?= $fecha ?></td>
                                                <td><?= $fechalimite ?></td>
                                                <td>
                                                    <span class="etiqueta etiqueta-completada">
                                                        <span class="punto-estado-solicitud completada"></span>
                                                        Finalizada
                                                    </span>
                                                </td>
                                                <td><span class="texto-apagado texto-xs">—</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>

            <!-- NOTIFICACIONES -->
            <div id="notificaciones" class="section" style="display:none;">
                <div class="tarjeta" style="max-width:600px;">
                    <div class="tarjeta-encabezado">
                        <div class="tarjeta-titulo">Notificaciones</div>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <p>Aquí aparecerán las notificaciones</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL: RECHAZAR REPORTE -->
    <div id="modalRechazo" class="fondo-modal">
        <div class="modal" style="max-width:440px;">
            <div class="modal-encabezado">
                <div class="modal-titulo">Rechazar Reporte</div>
                <button class="modal-cerrar" onclick="cerrarModalRechazo()">✕</button>
            </div>
            <div class="modal-divisor"></div>
            <p style="font-size:13px; color:#4d5a7a; margin-bottom:16px;">
                Indica el motivo del rechazo. El trabajador recibirá esta razón y deberá enviar un nuevo reporte.
            </p>
            <form id="formRechazo" method="POST" action="php/controlador_solicitud.php">
                <input type="hidden" name="accion" value="rechazar">
                <input type="hidden" name="id_sol" id="rechazo-id-sol">
                <div class="grupo-form">
                    <label class="etiqueta-form" for="razon-rechazo">Motivo del rechazo</label>
                    <textarea class="campo-form" id="razon-rechazo" name="razon" rows="4"
                        placeholder="Describe por qué rechazas este reporte..." required></textarea>
                </div>
                <div class="modal-pie">
                    <button type="submit" class="btn btn-peligro">Confirmar Rechazo</button>
                    <button type="button" class="btn btn-fantasma" onclick="cerrarModalRechazo()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/comun.js"></script>
    <script src="js/usuarios.js"></script>
    <script>
        function abrirModalRechazo(idSol) {
            document.getElementById('rechazo-id-sol').value = idSol;
            document.getElementById('modalRechazo').classList.add('abierto');
        }
        function cerrarModalRechazo() {
            document.getElementById('modalRechazo').classList.remove('abierto');
            document.getElementById('razon-rechazo').value = '';
        }
        document.getElementById('modalRechazo').addEventListener('click', function(e) {
            if (e.target === this) cerrarModalRechazo();
        });
    </script>
</body>
</html>