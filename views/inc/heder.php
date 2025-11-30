<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="public/css/submenus.css">

<!-- Barra de Navegación Superior -->
<nav class="barra-navegacion">
    <div class="seccion-izquierda">
        <button class="boton-menu" id="alternadorMenu">
            <i class="fas fa-bars"></i>
        </button>
        <span class="logotipo"><?= APP_NAME ?></span>
        <img src="./public/img/LOGO1.jpg" alt="Bienvenida" class="logo-upel">
    </div>
    
    <div class="seccion-derecha">
        <div class="contenedor-usuario">
            <div class="perfil-usuario" id="notifUsuario">
                <i class="fas fa-user-circle"></i>
                <span><?= $_SESSION['nombre'] ?></span>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
            </div>
            <div class="menu-desplegable" id="menuDesplegableUsuario">
                <button id="abrirConfiguracion" name="abrirConfiguracion">
                    <i class="fas fa-cog"></i>
                    <span>Configuracion</span>
                </button>
                <a href="./">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </div>
    <div class="seccion-derecha" style="position:absolute; right:250px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer;""">
        <div class="contenedor-notif">
            <div class="perfil-usuario" id="perfilUsuario">
                <i class="far fa-bell"></i>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
            </div>
        </div>
    </div>
</nav>

<!-- Barra Lateral de Navegación -->
<aside class="barra-lateral" id="barraLateral">
    <div class="navegacion-lateral">
        <ul>
            <li>
                <a href="?action=admin&method=home" class="active">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="#">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventario</span>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="?action=inventario&method=categorias">
                            <i class="fas fa-boxes"></i>
                            <span>Categorias</span>
                        </a>
                    </li>
                    <li>
                        <a href="?action=inventario&method=home">
                            <i class="fas fa-box"></i>
                            <span>Productos</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="has-submenu">
                <a href="#">
                    <i class="fas fa-users"></i>
                    <span>Departamentos</span>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="?action=oficinas&method=directores">
                            <i class="fas fa-user-check"></i>
                            <span>Directores</span>
                        </a>
                    </li>
                    <li>
                        <a href="?action=oficinas&method=home">
                            <i class="fa fa-users"></i>
                            <span>Oficinas</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="?action=proveedor&method=home">
                    <i class="fas fa-truck"></i>
                    <span>Proveedores</span>
                </a>
            </li>
            <li>
                <a href="?action=reporte&method=home">
                    <i class="fas fa-chart-bar"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
            <li>
                <a href="?action=solicitudes&method=home">
                    <i class="fas fa-receipt"></i>
                    <span>Solicitudes</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Modal de Verificación -->
<div id="modalVerificacion" class="ventana-modal">
    <form action="" method="post" class="formulario-verificacion" id="formularioVerificacion" autocomplete="off">
        <span class="boton-cerrar">&times;</span>
        <h2>Validación de Clave</h2>
        <label for="campoClaveSeguridad">Clave Superior:</label>
        <div style="position:relative;">
            <input type="password" name="clave" id="campoClaveSeguridad" class="campo-clave" required>
            <button type="button" id="alternadorVisibilidad" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer;">
                <i class="fas fa-eye" id="iconoOjo"></i>
            </button>
        </div>
        <button type="submit" name="verificar" id="botonVerificar" class="boton-verificar">Verificar</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script para el Modal de Verificación -->
<script>
    const ventanaModal = document.getElementById('modalVerificacion');
    const btnAbrirConfig = document.getElementById('abrirConfiguracion');
    const btnCerrarModal = document.querySelector('.boton-cerrar');
    const btnAlternarVisibilidad = document.getElementById('alternadorVisibilidad');
    const inputClave = document.getElementById('campoClaveSeguridad');
    const iconoVisibilidad = document.getElementById('iconoOjo');
    const claveSeguridad = "<?=APP_Password?>";

    // Abrir modal
    btnAbrirConfig.addEventListener("click", () => {
        ventanaModal.style.display = "flex";
    });

    // Cerrar modal
    btnCerrarModal.addEventListener("click", () => {
        ventanaModal.style.display = "none";
        inputClave.value = '';
    });

    // Mostrar/ocultar clave
    btnAlternarVisibilidad.addEventListener("click", () => {
        if (inputClave.type === "password") {
            inputClave.type = "text";
            iconoVisibilidad.classList.remove("fa-eye");
            iconoVisibilidad.classList.add("fa-eye-slash");
        } else {
            inputClave.type = "password";
            iconoVisibilidad.classList.remove("fa-eye-slash");
            iconoVisibilidad.classList.add("fa-eye");
        }
    });

    // Cerrar modal al hacer clic fuera del formulario
    window.addEventListener("click", (evento) => {
        if (evento.target === ventanaModal) {
            ventanaModal.style.display = "none";
        }
    });

    // Verificación de clave y redirección a configuración
    document.getElementById('formularioVerificacion').addEventListener('submit', function(evento){
        evento.preventDefault();
        console.log(claveSeguridad)
        if(inputClave.value === claveSeguridad){
            window.location.href = "?action=config&method=home";
        } else {
            ventanaModal.style.display = 'none';
            Swal.fire({
                title: 'ERROR',
                text: 'Error, la clave ingresada es incorrecta',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Volver a intentar',
                cancelButtonText: 'Cancelar'
            }).then((resultado) => {
                if(resultado.isConfirmed){
                    ventanaModal.style.display = 'flex';
                    inputClave.value = '';
                }
            });
        }
    });
</script>

<!-- Script para Navegación -->
<script>
    // Alternar menú desplegable de usuario
    const elementoPerfil = document.getElementById('notifUsuario'); //INTERCAMBIO CON EL DE NOTIFICACIONES
    const menuUsuario = document.getElementById('menuDesplegableUsuario');

    elementoPerfil.addEventListener('click', function(evento) {
        evento.stopPropagation();
        menuUsuario.classList.toggle('mostrar');
    });

    //Notificaciones redireccionar(tal vez menu en el futuro)
    const elementoNotif = document.getElementById('perfilUsuario'); //POR QUE? PUES FUNCIONO Y YA

    elementoNotif.addEventListener('click', function(evento) {
        evento.stopPropagation();
        window.location.href = "?action=notificaciones&method=home"
    });

    // Cerrar menú desplegable al hacer clic fuera
    document.addEventListener('click', function(evento) {
        if (!elementoPerfil.contains(evento.target) && !menuUsuario.contains(evento.target)) {
            menuUsuario.classList.remove('mostrar');
        }
    });

    // Alternar barra lateral
    const botonAlternador = document.getElementById('alternadorMenu');
    const barraNavegacion = document.getElementById('barraLateral');
    const contenidoPrincipal = document.querySelector('.contenido-principal');

    botonAlternador.addEventListener('click', function() {
        // Para desktop: contraer/expandir
        if (window.innerWidth > 768) {
            barraNavegacion.classList.toggle('contraida');
            if (contenidoPrincipal) {
                contenidoPrincipal.classList.toggle('expandido');
            }
        } else {
            // Para móvil: mostrar/ocultar
            barraNavegacion.classList.toggle('visible');
        }
    });

    // Cerrar barra lateral en móvil al hacer clic en un enlace
    const enlacesNavegacion = document.querySelectorAll('.navegacion-lateral a');
    enlacesNavegacion.forEach(enlace => {
        enlace.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                barraNavegacion.classList.remove('visible');
            }
        });
    });
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los elementos con submenús
            const submenuItems = document.querySelectorAll('.has-submenu');
            
            // Añadir evento de clic a cada elemento con submenú
            submenuItems.forEach(item => {
                const link = item.querySelector('a');
                
                link.addEventListener('click', function(e) {
                    // Prevenir la navegación si es un enlace vacío
                    if (this.getAttribute('href') === '#') {
                        e.preventDefault();
                    }
                    
                    // Cerrar otros submenús abiertos
                    submenuItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('open')) {
                            otherItem.classList.remove('open');
                        }
                    });
                    
                    // Alternar el estado del submenú actual
                    item.classList.toggle('open');
                });
            });
            
            // Cerrar submenús al hacer clic fuera del menú
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.navegacion-lateral')) {
                    submenuItems.forEach(item => {
                        item.classList.remove('open');
                    });
                }
            });
        });
</script>