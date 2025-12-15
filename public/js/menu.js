
async function obtenerClave() {
    try {
        const response = await fetch('?action=config&method=verif');
        const data = await response.json();
        const claveSeguridad = data.claveSeguridad;
        console.log("Clave de seguridad:", claveSeguridad);
        return claveSeguridad;
    } catch (error) {
        console.error("Error:", error);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar Lucide Icons primero
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // ===== Modal de Verificación =====
    const ventanaModal = document.getElementById('modalVerificacion');
    const btnAbrirConfig = document.getElementById('abrirConfiguracion');
    const btnCerrarModal = document.querySelector('.boton-cerrar');
    const btnAlternarVisibilidad = document.getElementById('alternadorVisibilidad');
    const inputClave = document.getElementById('campoClaveSeguridad');
    const iconoVisibilidad = document.getElementById('iconoOjo');

    // Check if element exists before accessing value
    const claveSeguridadElement = document.getElementById('claveSeguridad');
    let claveSeguridad = claveSeguridadElement ? claveSeguridadElement.value : '';

    // Abrir Modal
    if (btnAbrirConfig) {
        btnAbrirConfig.addEventListener("click", async function (e) {
            e.preventDefault();
            e.stopPropagation();
            const claves = await obtenerClave();
            if (claves) {
                claveSeguridad = claves.claveSuper;
            }

            // Mostrar modal inmediatamente
            if (ventanaModal) {
                ventanaModal.style.display = "flex";

                setTimeout(() => {
                    if (inputClave) {
                        inputClave.focus();
                    }
                }, 100);
            }
        });
    }
    // Removed the else block that logged "Botón configuración NO encontrado"

    // Cerrar modal
    if (btnCerrarModal) {
        btnCerrarModal.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            ventanaModal.style.display = "none";
            if (inputClave) inputClave.value = '';
        });
    }

    // Mostrar/ocultar clave
    if (btnAlternarVisibilidad && iconoVisibilidad) {
        btnAlternarVisibilidad.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (inputClave.type === "password") {
                inputClave.type = "text";
                iconoVisibilidad.setAttribute('data-lucide', 'eye-off');
            } else {
                inputClave.type = "password";
                iconoVisibilidad.setAttribute('data-lucide', 'eye');
            }

            // Re-render icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener("click", function (evento) {
        if (evento.target === ventanaModal) {
            ventanaModal.style.display = "none";
            if (inputClave) inputClave.value = '';
        }
    });

    // Verificación de clave
    const formularioVerificacion = document.getElementById('formularioVerificacion');
    if (formularioVerificacion) {
        formularioVerificacion.addEventListener('submit', function (evento) {
            evento.preventDefault();
            evento.stopPropagation();

            if (inputClave.value === claveSeguridad) {
                window.location.href = "?action=config&method=home";
            } else {
                ventanaModal.style.display = 'none';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Acceso Denegado',
                        text: 'La clave ingresada es incorrecta',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#3F51B5',
                        cancelButtonColor: '#E44336',
                        confirmButtonText: 'Volver a intentar',
                        cancelButtonText: 'Cancelar'
                    }).then((resultado) => {
                        if (resultado.isConfirmed) {
                            ventanaModal.style.display = 'flex';
                            inputClave.value = '';
                            setTimeout(() => {
                                inputClave.focus();
                            }, 100);
                        }
                    });
                }
            }
        });
    }

    // ===== Navegación =====
    // Menú desplegable de usuario
    const elementoPerfil = document.getElementById('notifUsuario');
    const menuUsuario = document.getElementById('menuDesplegableUsuario');

    if (elementoPerfil && menuUsuario) {
        elementoPerfil.addEventListener('click', function (evento) {
            evento.preventDefault();
            evento.stopPropagation();
            menuUsuario.classList.toggle('mostrar');
        });
    }

    // Notificaciones
    const elementoNotif = document.getElementById('perfilUsuario');
    if (elementoNotif) {
        elementoNotif.addEventListener('click', function (evento) {
            evento.preventDefault();
            evento.stopPropagation();
            window.location.href = "?action=notificaciones&method=home";
        });
    }

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function (evento) {
        if (elementoPerfil && menuUsuario) {
            if (!elementoPerfil.contains(evento.target) && !menuUsuario.contains(evento.target)) {
                menuUsuario.classList.remove('mostrar');
            }
        }
    });

    // Alternar barra lateral
    const botonAlternador = document.getElementById('alternadorMenu');
    const barraNavegacion = document.getElementById('barraLateral');
    // FIXED: Changed selector from .contenido-principal to .main-content
    const contenidoPrincipal = document.querySelector('.main-content');

    // Checks localStorage for saved state
    if (localStorage.getItem('sidebarState') === 'collapsed' && window.innerWidth > 768) {
        if (barraNavegacion) barraNavegacion.classList.add('contraida');
        if (contenidoPrincipal) contenidoPrincipal.classList.add('expandido');
    }

    if (botonAlternador && barraNavegacion) {
        botonAlternador.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (window.innerWidth > 768) {
                // Desktop: contraer/expandir
                barraNavegacion.classList.toggle('contraida');
                if (contenidoPrincipal) {
                    contenidoPrincipal.classList.toggle('expandido');
                }

                // Save state
                if (barraNavegacion.classList.contains('contraida')) {
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    localStorage.setItem('sidebarState', 'expanded');
                }

            } else {
                // Móvil: mostrar/ocultar
                barraNavegacion.classList.toggle('visible');
            }
        });
    }

    // Cerrar barra lateral en móvil al hacer clic en enlace
    const enlacesNavegacion = document.querySelectorAll('.navegacion-lateral a');
    enlacesNavegacion.forEach(enlace => {
        enlace.addEventListener('click', function () {
            if (window.innerWidth <= 768 && barraNavegacion) {
                barraNavegacion.classList.remove('visible');
            }
        });
    });

    // Submenús
    const submenuItems = document.querySelectorAll('.has-submenu');

    submenuItems.forEach(item => {
        const link = item.querySelector('a');

        if (link) {
            link.addEventListener('click', function (e) {
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                }

                // Cerrar otros submenús
                submenuItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('open')) {
                        otherItem.classList.remove('open');
                        const arrow = otherItem.querySelector('.submenu-arrow');
                        if (arrow) {
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                // Alternar submenú actual
                item.classList.toggle('open');

                // Rotar flecha
                const arrow = this.querySelector('.submenu-arrow');
                if (arrow) {
                    if (item.classList.contains('open')) {
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                }
            });
        }
    });
});
