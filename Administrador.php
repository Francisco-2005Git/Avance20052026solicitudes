<!-- PHP -->
<!-- Comprobración de sesión -->
<?php
session_start();
if (empty($_SESSION["id"]) || !is_numeric($_SESSION["id"])) {
    header("location: login.php");
    exit();
}

$msgExito = $_SESSION["exito"] ?? null;
$msgError = $_SESSION["error"] ?? null;
$seccionActiva = $_SESSION["seccion_activa"] ?? null;
unset($_SESSION["exito"], $_SESSION["error"], $_SESSION["seccion_activa"]);
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Solicitudes - Administrador — ITSRV</title>
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
                <div class="usuario-avatar" style="background-color:#3d6bbf;">AD</div>
                <div>
                    <div class="usuario-nombre">
                        <?php 
                            echo $_SESSION["nombre"]. " " .$_SESSION["app"];
                        ?>
                    </div>
                    <div class="usuario-rol">Control total</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-etiqueta-seccion">Panel</div>
            <a href="#" class="nav-link nav-item active" data-section="bitacora">
                Bitácora
            </a>
            <a href="#" class="nav-link nav-item" data-section="generar-reporte">
                Generar Reporte
            </a>
            <a href="#" class="nav-link nav-item" data-section="admin-usuarios">
                Administrar Usuarios
            </a>
        </nav>
        <!-- El controlador PHP hace el trabajo de cerrar sesión -->
        <div class="sidebar-pie">
            <a href="php/controlador_cerrar.php" class="btn-cerrar-sesion">
                <span>❌</span> Cerrar Sesión
            </a>
        </div>
    </aside>

    <div class="contenido-principal">

        <header class="topbar">
            <div>
                <div class="topbar-titulo" id="topbar-titulo">Bitácora</div>
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

            <div id="bitacora" class="section active">
                <div class="tarjeta">
                    <div class="tarjeta-encabezado">
                        <div class="tarjeta-titulo">Bitácora de solicitudes</div>
                    </div>

                    <div class="barra-herramientas">
                        <div class="campo-busqueda">
                            <input class="campo-form" type="text" placeholder="Buscar en bitácora...">
                        </div>
                        <select class="campo-form" style="width:auto; min-width:140px;">
                            <option value="">Todos los estados</option>
                            <option>Pendiente</option>
                            <option>En proceso</option>
                            <option>Completada</option>
                        </select>
                        <select class="campo-form" style="width:auto; min-width:140px;">
                            <option value="">Todas las áreas</option>
                            <option>Docencia</option>
                            <option>Coordinación Académica</option>
                            <option>Servicios Escolares</option>
                            <option>Recursos Humanos</option>
                        </select>
                    </div>

                    <div class="contenedor-tabla">
                        <table>
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Trabajador Asignado</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Detalles Resumidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos de ejemplo; en un sistema real, esto vendría de un backend -->
                                <tr>
                                    <td>Juan Pérez</td>
                                    <td>Carlos López</td>
                                    <td><span class="etiqueta etiqueta-completada">Finalizada</span></td>
                                    <td class="texto-apagado">2023-10-01</td>
                                    <td>Solicitud de soporte técnico resuelta.</td>
                                </tr>
                                <tr>
                                    <td>María García</td>
                                    <td>Ana Rodríguez</td>
                                    <td><span class="etiqueta etiqueta-proceso">En curso</span></td>
                                    <td class="texto-apagado">2023-09-28</td>
                                    <td>Actualización de software en proceso.</td>
                                </tr>
                                <tr>
                                    <td>Pedro Sánchez</td>
                                    <td>Carlos López</td>
                                    <td><span class="etiqueta etiqueta-pendiente">Aceptada</span></td>
                                    <td class="texto-apagado">2023-09-25</td>
                                    <td>Reporte de bug aceptado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="generar-reporte" class="section" style="display:none;">
                <div class="tarjeta" style="max-width:620px;">
                    <div class="tarjeta-encabezado">
                        <div class="tarjeta-titulo">Generar Reporte</div>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <form action="#" method="post">

                            <div class="grupo-form">
                                <label class="etiqueta-form" for="tipo-reporte">Tipo de reporte</label>
                                <select class="campo-form" id="tipo-reporte" name="tipo-reporte" required>
                                    <option value="">— Selecciona tipo —</option>
                                    <option value="mensual">Mensual</option>
                                    <option value="anual">Anual</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>

                            <div class="fila-form">
                                <div class="grupo-form">
                                    <label class="etiqueta-form" for="fecha-inicio">Fecha de inicio</label>
                                    <input class="campo-form" type="date" id="fecha-inicio" name="fecha-inicio" required>
                                </div>
                                <div class="grupo-form">
                                    <label class="etiqueta-form" for="fecha-fin">Fecha de fin</label>
                                    <input class="campo-form" type="date" id="fecha-fin" name="fecha-fin" required>
                                </div>
                            </div>

                            <div class="grupo-form">
                                <label class="etiqueta-form" for="descripcion-reporte">Descripción adicional</label>
                                <textarea class="campo-form" id="descripcion-reporte" name="descripcion-reporte" rows="4" placeholder="Describe el reporte"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primario">Generar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="admin-usuarios" class="section" style="display:none;">
                <div class="tarjeta">
                    <div class="tarjeta-encabezado">
                        <div class="tarjeta-titulo">Administrar Usuarios</div>
                        <button class="btn btn-primario btn-pequeno add-user" onclick="openModal('add')">Agregar Usuario</button>
                    </div>

                    <div class="barra-herramientas">
                        <div class="campo-busqueda">
                            <input class="campo-form" type="text" placeholder="Buscar usuario...">
                        </div>
                        <select class="campo-form" style="width:auto; min-width:140px;">
                            <option value="">Todos los roles</option>
                            <option>Administrador</option>
                            <option>Trabajador</option>
                            <option>Usuario</option>
                        </select>
                    </div>

                    <div class="contenedor-tabla">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos de ejemplo -->
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar-fila-tabla">JP</div>
                                            <strong>Juan Pérez</strong>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-pendiente">Usuario</span></td>
                                    <td>
                                        <div class="acciones-tabla">
                                            <button class="btn btn-advertencia btn-pequeno edit" onclick="openModal('edit', 'Juan Pérez', 'usuario')">Editar</button>
                                            <button class="btn btn-peligro btn-pequeno delete" onclick="deleteUser('Juan Pérez')">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar-fila-tabla">CL</div>
                                            <strong>Carlos López</strong>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-proceso">Trabajador</span></td>
                                    <td>
                                        <div class="acciones-tabla">
                                            <button class="btn btn-advertencia btn-pequeno edit" onclick="openModal('edit', 'Carlos López', 'trabajador')">Editar</button>
                                            <button class="btn btn-peligro btn-pequeno delete" onclick="deleteUser('Carlos López')">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar-fila-tabla">MG</div>
                                            <strong>María García</strong>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-pendiente">Usuario</span></td>
                                    <td>
                                        <div class="acciones-tabla">
                                            <button class="btn btn-advertencia btn-pequeno edit" onclick="openModal('edit', 'María García', 'usuario')">Editar</button>
                                            <button class="btn btn-peligro btn-pequeno delete" onclick="deleteUser('María García')">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Sección para agregar/editar usuario -->
    <div id="userModal" class="fondo-modal">
        <div class="modal">
            <div class="modal-encabezado">
                <div class="modal-titulo" id="modal-title">Agregar Usuario</div>
                <button class="modal-cerrar" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-divisor"></div>

            <form id="userForm" method="POST" action="php/controlador_usuario.php">
                <input type="hidden" name="accion" value="agregar">

                <!-- Nombre -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-nombre">Nombre</label>
                    <input class="campo-form" type="text" id="user-nombre"
                        name="nombre" maxlength="50" required>
                </div>

                <!-- Apellido Paterno -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-app">Apellido Paterno</label>
                    <input class="campo-form" type="text" id="user-app"
                        name="app" maxlength="50" required>
                </div>

                <!-- Apellido Materno -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-apm">Apellido Materno</label>
                    <input class="campo-form" type="text" id="user-apm"
                        name="apm" maxlength="50"
                        placeholder="Opcional">
                </div>

                <!-- Username -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-name">Usuario/Username</label>
                    <input class="campo-form" type="text" id="user-name"
                        name="username" maxlength="50" required>
                </div>

                <!-- Contraseña -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-password">Contraseña</label>
                    <input class="campo-form" type="password" id="user-password"
                        name="password" minlength="8" maxlength="255" required>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-password2">Confirmar Contraseña</label>
                    <input class="campo-form" type="password" id="user-password2"
                        name="password2" minlength="8" maxlength="255" required>
                </div>

                <!-- Rol -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-role">Rol</label>
                    <select class="campo-form" id="user-role" name="id_rol" required>
                        <option value="" disabled selected>Seleccionar...</option>
                        <option value="1">Usuario</option>
                        <option value="2">Trabajador</option>
                    </select>
                </div>

                <!-- Área -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-area">Área</label>
                    <select class="campo-form" id="user-area" name="id_area" required>
                        <option value="" disabled selected>Seleccionar...</option>
                        <option value="1">Docencia</option>
                        <option value="2">Coordinación Académica</option>
                        <option value="3">Servicios Escolares</option>
                        <option value="4">Recursos Humanos</option>
                    </select>
                </div>

                <!-- Disponible (relevante para trabajadores) -->
                <div class="grupo-form">
                    <label class="etiqueta-form" for="user-disponible">Disponible</label>
                    <select class="campo-form" id="user-disponible" name="disponible">
                        <option value="1" selected>Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="modal-pie">
                    <button type="submit" class="btn btn-primario">Guardar</button>
                    <button type="button" class="btn btn-fantasma" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/comun.js"></script>
    <script src="js/administrador.js"></script>

    <?php if ($seccionActiva): ?>
    <script>
        navegarSeccion("<?= htmlspecialchars($seccionActiva) ?>", titulosSecciones);
    </script>
    <?php endif; ?>
</body>
</html>