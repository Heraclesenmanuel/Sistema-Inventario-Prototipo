<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Estilos -->
<link rel="stylesheet" href="public/css/submenus.css">

<!-- Barra de Navegación Superior -->
<nav class="barra-navegacion" role="navigation" aria-label="Navegación principal">
    <div class="seccion-izquierda">
        <button class="boton-menu" id="alternadorMenu" aria-label="Alternar menú lateral">
            <i data-lucide="menu" class="menu-icon"></i>
        </button>
        <div class="brand-container">
            <img src="./public/img/LOGO1.jpg" alt="Logo UPEL" class="logo-upel">
            <span class="logotipo"><?= APP_NAME ?></span>
        </div>
    </div>
    
    <div class="seccion-derecha">
        <!-- Notificaciones -->
        <div class="contenedor-notif">
            <button class="btn-notif" id="perfilUsuario" aria-label="Ver notificaciones">
                <i data-lucide="bell" class="notif-icon"></i>
                <span class="notif"></span>
            </button>
        </div>
        
        <!-- Usuario -->
        <div class="contenedor-usuario">
            <button class="perfil-usuario" id="notifUsuario" aria-label="Menú de usuario">
                <div class="user-avatar">
                    <i data-lucide="user-circle" class="avatar-icon"></i>
                </div>
                <span class="user-name"><?= $_SESSION['nombre'] ?></span>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </button>
            
            <div class="menu-desplegable" id="menuDesplegableUsuario" role="menu">
                <?php if($_SESSION['dpto'] == 1): ?>
                <button id="abrirConfiguracion" name="abrirConfiguracion" class="menu-item" role="menuitem">
                    <i data-lucide="settings" class="menu-item-icon"></i>
                    <span>Configuración</span>
                <?php endif; ?>
                </button>
                <a href="?action=admin&method=cerrar" class="menu-item" role="menuitem">
                    <i data-lucide="log-out" class="menu-item-icon"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Barra Lateral de Navegación -->
<aside class="barra-lateral" id="barraLateral" role="complementary" aria-label="Menú de navegación">
        <?php 
        $currentAction = $_GET['action'] ?? 'admin';
        $currentMethod = $_GET['method'] ?? '';
        ?>
        <ul role="menu">
            <li role="none">
                <a href="?action=admin&method=home" class="nav-link <?= $currentAction == 'admin' ? 'active' : '' ?>" role="menuitem">
                    <i data-lucide="home" class="nav-icon"></i>
                    <span class="nav-text">Inicio</span>
                </a>
            </li>
            <li class="has-submenu <?= $currentAction == 'inventario' ? 'open' : '' ?>" role="none">
                <a href="#" class="nav-link <?= $currentAction == 'inventario' ? 'active' : '' ?>" role="menuitem" aria-haspopup="true" aria-expanded="<?= $currentAction == 'inventario' ? 'true' : 'false' ?>">
                    <i data-lucide="package" class="nav-icon"></i>
                    <span class="nav-text">Inventario</span>
                    <i data-lucide="chevron-down" class="submenu-arrow" style="<?= $currentAction == 'inventario' ? 'transform: rotate(180deg)' : '' ?>"></i>
                </a>
                <ul class="submenu" role="menu">
                    <?php if($_SESSION['dpto'] != 2 && $_SESSION['dpto'] != 4): ?>
                    <li role="none">
                        <a href="?action=inventario&method=categorias" class="submenu-link <?= ($currentAction == 'inventario' && $currentMethod == 'categorias') ? 'active' : '' ?>" role="menuitem">
                            <i data-lucide="folder" class="submenu-icon"></i>
                            <span>Categorías</span>
                        </a>
                    </li>
                <?php endif; ?>
                    <li role="none">
                        <a href="?action=inventario&method=home" class="submenu-link <?= ($currentAction == 'inventario' && $currentMethod == 'home') ? 'active' : '' ?>" role="menuitem">
                            <i data-lucide="box" class="submenu-icon"></i>
                            <span>Productos</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php if ($_SESSION['dpto'] !=2 && $_SESSION['dpto'] !=4): ?>
            <li class="has-submenu <?= $currentAction == 'oficinas' ? 'open' : '' ?>" role="none">
                <a href="#" class="nav-link <?= $currentAction == 'oficinas' ? 'active' : '' ?>" role="menuitem" aria-haspopup="true" aria-expanded="<?= $currentAction == 'oficinas' ? 'true' : 'false' ?>">
                    <i data-lucide="building-2" class="nav-icon"></i>
                    <span class="nav-text">Departamentos</span>
                    <i data-lucide="chevron-down" class="submenu-arrow" style="<?= $currentAction == 'oficinas' ? 'transform: rotate(180deg)' : '' ?>"></i>
                </a>
                <ul class="submenu" role="menu">
                    <li role="none">
                        <a href="?action=oficinas&method=directores" class="submenu-link <?= ($currentAction == 'oficinas' && $currentMethod == 'directores') ? 'active' : '' ?>" role="menuitem">
                            <i data-lucide="user-check" class="submenu-icon"></i>
                            <span>Directores</span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="?action=oficinas&method=home" class="submenu-link <?= ($currentAction == 'oficinas' && $currentMethod == 'home') ? 'active' : '' ?>" role="menuitem">
                            <i data-lucide="users" class="submenu-icon"></i>
                            <span>Oficinas</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if($_SESSION['dpto'] !=2): ?>
            <li role="none">
                <a href="?action=proveedor&method=home" class="nav-link <?= $currentAction == 'proveedor' ? 'active' : '' ?>" role="menuitem">
                    <i data-lucide="truck" class="nav-icon"></i>
                    <span class="nav-text">Proveedores</span>
                </a>
            </li>
            <?php endif; ?>
            <li role="none">
                <a href="?action=reporte&method=home" class="nav-link <?= $currentAction == 'reporte' ? 'active' : '' ?>" role="menuitem">
                    <i data-lucide="bar-chart-3" class="nav-icon"></i>
                    <span class="nav-text">Estadísticas</span>
                </a>
            </li>
            <li role="none">
                <a href="?action=solicitudes&method=home" class="nav-link <?= $currentAction == 'solicitudes' ? 'active' : '' ?>" role="menuitem">
                    <i data-lucide="file-text" class="nav-icon"></i>
                    <span class="nav-text">Solicitudes</span>
                </a>
            </li>
            <?php if($_SESSION['dpto'] == 2): ?>
            <li role="none">
                <a href="?action=notificaciones&method=home" class="nav-link <?= $currentAction == 'notificaciones' ? 'active' : '' ?>" role="menuitem">
                    <div style="position: relative; display: flex; align-items: center;">
                        <i data-lucide="bell" class="nav-icon"></i>
                        <!-- Optional dot for unread -->
                        <span style="position: absolute; top:0; right: 14px; width: 8px; height: 8px; background: var(--rojo-vibrante); border-radius: 50%; opacity: 0.8;"></span>
                    </div>
                    <span class="nav-text">Notificaciones</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<!-- Modal de Verificación -->
<div id="modalVerificacion" class="ventana-modal" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
    <article class="modal-content">
        <form action="" method="post" class="formulario-verificacion" id="formularioVerificacion" autocomplete="off">
            <header class="modal-header">
                <div class="modal-icon">
                    <i data-lucide="shield-check" class="icon-modal"></i>
                </div>
                <h2 id="modalTitle" class="modal-title">Validación de Seguridad</h2>
                <button type="button" class="boton-cerrar" aria-label="Cerrar modal">
                    <i data-lucide="x" class="close-icon"></i>
                </button>
            </header>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="campoClaveSeguridad" class="form-label">
                        <i data-lucide="key" class="label-icon"></i>
                        <span>Clave Superior</span>
                    </label>
                    <div class="input-wrapper password-wrapper">
                        <i data-lucide="lock" class="input-icon"></i>
                        <input 
                            type="password" 
                            name="clave" 
                            id="campoClaveSeguridad" 
                            class="form-input" 
                            placeholder="Ingresa la clave de seguridad"
                            required
                            autocomplete="off"
                            aria-label="Clave de seguridad"
                        >
                        <button 
                            type="button" 
                            id="alternadorVisibilidad" 
                            class="btn-toggle"
                            aria-label="Mostrar u ocultar clave"
                        >
                            <i data-lucide="eye" id="iconoOjo"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <footer class="modal-footer">
                <button type="submit" name="verificar" id="botonVerificar" class="btn-submit">
                    <i data-lucide="shield-check" class="btn-icon"></i>
                    <span>Verificar Acceso</span>
                </button>
            </footer>
        </form>
    </article>
</div>


<input type="hidden" id="claveSeguridad" value="<?= APP_Password ?>">


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="public/js/menu.js"></script>
