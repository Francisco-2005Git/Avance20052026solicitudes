<!-- PHP -->
<!-- Comprobación de sesión -->

<?php
session_start();
if (empty($_SESSION["id"]) || !is_numeric($_SESSION["id"]) || $_SESSION["id_rol"] != 1) {
    header("Location: index.php");
    exit();
}
$msgExito = $_SESSION["exito"] ?? null;
$msgError = $_SESSION["error"] ?? null;
unset($_SESSION["exito"], $_SESSION["error"]);
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Solicitudes — Usuario | ITSRV</title>
    <link rel="stylesheet" href="styles.css">
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
            <a href="#" class="nav-link nav-item active" data-section="crear">
                Nueva Solicitud
            </a>
            <a href="#" class="nav-link nav-item" data-section="creadas">
                Mis Solicitudes
                <span class="nav-contador">+45</span>
            </a>
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

            <div id="crear" class="section active">
                <div class="tarjeta" style="max-width: 680px;">
                        <div class="tarjeta-encabezado">
                            <div class="tarjeta-titulo">Nueva solicitud de soporte</div>
                        </div>
                        <div class="tarjeta-cuerpo">
                            <form action="#" method="post">

                                <div class="grupo-form">
                                    <label class="etiqueta-form" for="titulo">Título de la solicitud</label>
                                    <input class="campo-form" type="text" id="titulo" name="titulo" placeholder="Ej: Equipo sin acceso a red" required>
                                </div>

                                <div class="fila-form">
                                    <div class="grupo-form">
                                        <label class="etiqueta-form" for="area">Área</label>
                                        <select class="campo-form" id="area" name="area" required>
                                            <option value="">— Seleccionar —</option>
                                            <option>Coordinación Académica</option>
                                            <option>Servicios Escolares</option>
                                            <option>Recursos Humanos</option>
                                            <option>Dirección General</option>
                                            <option>Centro de Cómputo</option>
                                            <option>Biblioteca</option>
                                            <option>Docencia</option>
                                        </select>
                                    </div>
                                    <div class="grupo-form">
                                        <label class="etiqueta-form" for="prioridad">Prioridad</label>
                                        <select class="campo-form" id="prioridad" name="prioridad" required>
                                            <option value="">— Seleccionar —</option>
                                            <option value="alta">Alta</option>
                                            <option value="media">Media</option>
                                            <option value="baja">Baja</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grupo-form">
                                    <label class="etiqueta-form" for="descripcion">Descripción detallada</label>
                                    <textarea class="campo-form" id="descripcion" name="descripcion" rows="5" placeholder="Describe el problema con el mayor detalle posible" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primario w-full" style="justify-content:center; padding:10px;">
                                    Enviar Solicitud
                                </button>
                            </form>
                        </div>
                </div>
            </div>

            <div id="creadas" class="section" style="display:none;">
                <div class="tarjeta">
                    <div class="tarjeta-encabezado">
                        <div class="tarjeta-titulo">Mis solicitudes <span class="pastilla-contador">Era bait, solo hay 3</span></div>
                        <button class="btn btn-primario btn-pequeno" onclick="navTo('crear')">Nueva solicitud</button>
                    </div>

                    <div class="barra-filtros">
                        <div class="campo-busqueda">
                            <input class="campo-form" type="text" placeholder="Buscar solicitud...">
                        </div>
                        <select class="campo-form" style="width:auto; min-width:140px;">
                            <option value="">Todos los estados</option>
                            <option>Pendiente</option>
                            <option>En proceso</option>
                            <option>Completada</option>
                        </select>
                    </div>

                    <div class="contenedor-tabla">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Área</th>
                                    <th>Prioridad</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos de ejemplo para que mas o menos vean como quedaría la vaina-->
                                <tr>
                                    <td><span class="texto-apagado texto-xs">#067</span></td>
                                    <td><strong>Equipo sin acceso a red — Sala C-12</strong></td>
                                    <td>Coordinación Académica</td>
                                    <td><span class="etiqueta etiqueta-alta">Alta</span></td>
                                    <td class="texto-apagado">01/03/2025</td>
                                    <td><span class="etiqueta etiqueta-proceso"><span class="punto-estado-solicitud proceso"></span>En proceso</span></td>
                                </tr>
                                <tr>
                                    <td><span class="texto-apagado texto-xs">#117</span></td>
                                    <td><strong>Instalación de software contable</strong></td>
                                    <td>Recursos Humanos</td>
                                    <td><span class="etiqueta etiqueta-media">Media</span></td>
                                    <td class="texto-apagado">26/02/2025</td>
                                    <td><span class="etiqueta etiqueta-completada"><span class="punto-estado-solicitud completada"></span>Completada</span></td>
                                </tr>
                                <tr>
                                    <td><span class="texto-apagado texto-xs">#777</span></td>
                                    <td><strong>Proyector sin señal — Aula 304</strong></td>
                                    <td>Docencia</td>
                                    <td><span class="etiqueta etiqueta-baja">Baja</span></td>
                                    <td class="texto-apagado">20/02/2025</td>
                                    <td><span class="etiqueta etiqueta-pendiente"><span class="punto-estado-solicitud pendiente"></span>Pendiente</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="js/comun.js"></script>
    <script src="js/usuarios.js"></script>
</body>
</html>