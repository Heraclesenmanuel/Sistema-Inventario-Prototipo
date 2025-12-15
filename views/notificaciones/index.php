<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Sistema' ?> - Notificaciones</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">

    <!-- Estilos -->
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/notifications.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include_once 'views/inc/heder.php'; ?>
        </aside>

        <div class="main-content">
            <!-- Panel Principal Unificado -->
            <div class="main-panel">
                <!-- Header dentro del panel -->
                <div class="panel-header">
                    <div class="header-content">
                        <div class="header-title">
                            <h1>
                                🔔 Centro de Notificaciones
                                <?php
                                $unreadCount = 0;
                                foreach ($notificacionesConUsuarios ?? [] as $notif) {
                                    if (!$notif['leido']) {
                                        $unreadCount++;
                                    }
                                }
                                if ($unreadCount > 0): ?>
                                    <span class="unread-badge"><?= $unreadCount ?> sin leer</span>
                                <?php endif; ?>
                            </h1>
                            <p>Gestiona todas las notificaciones del sistema</p>
                        </div>
                        <div class="header-actions">
                            <button class="action-btn" onclick="marcarTodasComoLeidas()">
                                <i data-lucide="check-circle"></i>
                                Marcar todas como leídas
                            </button>
                        </div>
                    </div>
                </div>
                <br>
                <!-- Filtros dentro del mismo panel -->
                <div class="panel-filters">
                    <div class="filters-grid">
                        <div class="search-container">
                            <i data-lucide="search" class="search-icon"></i>
                            <input type="text" id="searchInput" class="search-input"
                                placeholder="Buscar en notificaciones..." onkeyup="filtrarNotificaciones()">
                        </div>

                        <div class="filter-group">
                            <button class="filter-btn active" onclick="filtrarPorTipo('todas')">
                                <i data-lucide="bell"></i>
                                Todas
                            </button>
                            <button class="filter-btn" onclick="filtrarPorTipo('no-leidas')">
                                <i data-lucide="mail"></i>
                                No leídas
                            </button>
                            <button class="filter-btn" onclick="filtrarPorTipo('urgentes')">
                                <i data-lucide="alert-octagon"></i>
                                Urgentes
                            </button>
                        </div>

                        <button class="action-btn secondary" onclick="limpiarNotificaciones()">
                            <i data-lucide="trash-2"></i>
                            Limpiar leídas
                        </button>
                    </div>
                </div>
                <br><br>
                <!-- Contenedor Principal -->
                <div class="notifications-container">
                    <!-- Estadísticas -->
                    <div class="stats-row">
                        <?php
                        $unreadCount = 0;
                        $urgentCount = 0;
                        $warningCount = 0;
                        $infoCount = 0;

                        foreach ($notificacionesConUsuarios ?? [] as $notif) {
                            if (!$notif['leido'])
                                $unreadCount++;
                            switch ($notif['tipo']) {
                                case 1:
                                    $urgentCount++;
                                    break;
                                case 2:
                                    $warningCount++;
                                    break;
                                case 3:
                                    $infoCount++;
                                    break;
                            }
                        }
                        ?>

                        <div class="stat-card" onclick="filtrarPorTipo('no-leidas')">
                            <div class="stat-icon unread">
                                <i data-lucide="mail"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="unreadCount"><?= $unreadCount ?></h3>
                                <p>No leídas</p>
                            </div>
                        </div>

                        <div class="stat-card" onclick="filtrarPorTipo('urgentes')">
                            <div class="stat-icon urgent">
                                <i data-lucide="alert-octagon"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="urgentCount"><?= $urgentCount ?></h3>
                                <p>Urgentes</p>
                            </div>
                        </div>

                        <div class="stat-card" onclick="filtrarPorTipo('advertencias')">
                            <div class="stat-icon warning">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="warningCount"><?= $warningCount ?></h3>
                                <p>Advertencias</p>
                            </div>
                        </div>

                        <div class="stat-card" onclick="filtrarPorTipo('informativas')">
                            <div class="stat-icon info">
                                <i data-lucide="info"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="infoCount"><?= $infoCount ?></h3>
                                <p>Informativas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Notificaciones -->
                    <div class="notifications-list" id="notificationsList">
                        <?php if (!empty($notificacionesConUsuarios)): ?>
                            <?php foreach ($notificacionesConUsuarios as $notif): ?>
                                <?php
                                // Determinar clase según tipo
                                $tipoClase = match ($notif['tipo']) {
                                    1 => 'urgent',
                                    2 => 'warning',
                                    3 => 'info',
                                    4 => 'success',
                                    default => 'info'
                                };

                                $iconClass = match ($notif['tipo']) {
                                    1 => 'icon-urgent',
                                    2 => 'icon-warning',
                                    3 => 'icon-info',
                                    4 => 'icon-success',
                                    default => 'icon-info'
                                };

                                $iconName = match ($notif['tipo']) {
                                    1 => 'alert-octagon',
                                    2 => 'alert-triangle',
                                    3 => 'info',
                                    4 => 'check-circle',
                                    default => 'info'
                                };

                                $badgeClass = match ($notif['tipo']) {
                                    1 => 'badge-urgent',
                                    2 => 'badge-warning',
                                    3 => 'badge-info',
                                    4 => 'badge-success',
                                    default => 'badge-info'
                                };

                                // Contar destinatarios
                                $destinatarios = $notif['usuarios'] ?? [];
                                $totalDestinatarios = count($destinatarios);
                                $primerosDestinatarios = array_slice($destinatarios, 0, 2);
                                ?>

                                <div class="notification-item <?= $tipoClase ?> <?= !$notif['leido'] ? 'unread' : '' ?>"
                                    data-id="<?= $notif['id_notif'] ?>" data-tipo="<?= $notif['tipo'] ?>"
                                    data-leido="<?= $notif['leido'] ? '1' : '0' ?>">

                                    <?php if (!$notif['leido']): ?>
                                        <div class="unread-dot"></div>
                                    <?php endif; ?>

                                    <div class="notification-icon <?= $iconClass ?>">
                                        <i data-lucide="<?= $iconName ?>"></i>
                                    </div>

                                    <div class="notification-content">
                                        <div class="notification-message truncate">

                                            <?= htmlspecialchars($notif['mensaje'] ?? 'Sin mensaje') ?>
                                        </div>

                                        <div class="notification-meta">
                                            <div class="meta-item">
                                                <i data-lucide="calendar"></i>
                                                <span><?= date('d/m/Y', strtotime($notif['fecha_notif'])) ?></span>
                                            </div>

                                            <div class="meta-item">
                                                <i data-lucide="clock"></i>
                                                <span><?= date('h:i A', strtotime($notif['fecha_notif'])) ?></span>
                                                <span class="relative-time" data-fecha="<?= $notif['fecha_notif'] ?>"></span>
                                            </div>
                                        </div>

                                        <?php if ($totalDestinatarios > 0): ?>
                                            <div class="notification-recipients">
                                                <i data-lucide="users"
                                                    style="width: 14px; height: 14px; color: var(--gray-500);"></i>
                                                <?php foreach ($primerosDestinatarios as $destinatario): ?>
                                                    <span class="recipient-badge">
                                                        <i data-lucide="user" style="width: 12px; height: 12px;"></i>
                                                        <?= htmlspecialchars($destinatario['nombre'] ?? 'Usuario') ?>
                                                    </span>
                                                <?php endforeach; ?>

                                                <?php if ($totalDestinatarios > 2): ?>
                                                    <span class="recipient-count">
                                                        +<?= $totalDestinatarios - 2 ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="notification-actions">
                                        <?php if (!$notif['leido']): ?>
                                            <button class="action-btn-small mark-read"
                                                onclick="marcarComoLeida(<?= $notif['id_notif'] ?>, this)"
                                                title="Marcar como leída">
                                                <i data-lucide="mail-open"></i>
                                            </button>
                                        <?php endif; ?>

                                        <button class="action-btn-small"
                                            onclick="verDetalles(<?= htmlspecialchars(json_encode($notif)) ?>)"
                                            title="Ver detalles">
                                            <i data-lucide="eye"></i>
                                        </button>

                                        <button class="action-btn-small delete"
                                            onclick="eliminarNotificacion(<?= $notif['id_notif'] ?>, this)" title="Eliminar">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i data-lucide="bell-off"></i>
                                </div>
                                <h3>No hay notificaciones</h3>
                                <p>Todas las notificaciones están gestionadas o no hay nuevas alertas.</p>
                                <button class="action-btn" onclick="recargarNotificaciones()" style="margin-top: 1rem;">
                                    <i data-lucide="refresh-cw"></i>
                                    Recargar notificaciones
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Detalles -->
        <div class="modal-overlay" id="detailsModal">
            <div class="modal-content" id="modalContent">
                <!-- Contenido dinámico -->
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Inicializar Iconos
            lucide.createIcons();

            // Estado global
            let currentUserId = <?= $_SESSION['id'] ?? 0 ?>;
            let notificacionesData = <?= json_encode($notificacionesConUsuarios ?? []) ?>;
            let currentName = "<?= $_SESSION['nombre'] ?? '' ?>";
            let currentFilter = 'todas';

            // Configurar tiempo relativo
            document.querySelectorAll(".relative-time").forEach(el => {
                const fecha = el.dataset.fecha;
                el.textContent = obtenerTiempoRelativo(fecha);
            });

            // ========== FUNCIONES DE FILTRADO ==========

            // Función llamada desde los botones onclick
            function filtrarPorTipo(tipo) {
                // Actualizar botón activo basado en el tipo
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                    const btnText = btn.textContent.toLowerCase();
                    if (
                        (tipo === 'todas' && btnText.includes('todas')) ||
                        (tipo === 'no-leidas' && (btnText.includes('no leídas') || btnText.includes('no-leidas'))) ||
                        (tipo === 'urgentes' && btnText.includes('urgentes')) ||
                        (tipo === 'advertencias' && btnText.includes('advertencias')) ||
                        (tipo === 'informativas' && btnText.includes('informativas'))
                    ) {
                        btn.classList.add('active');
                    }
                });

                currentFilter = tipo;
                aplicarFiltros();
            }

            // Función llamada desde el input de búsqueda (onkeyup)
            function filtrarNotificaciones() {
                aplicarFiltros();
            }

            // ========== FUNCIÓN PRINCIPAL DE FILTRADO ==========
            function aplicarFiltros() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const items = document.querySelectorAll('.notification-item');
                let visibleCount = 0;
                let unreadCount = 0;
                let urgentCount = 0;
                let warningCount = 0;
                let infoCount = 0;

                items.forEach(item => {
                    const texto = item.textContent.toLowerCase();
                    const tipo = item.dataset.tipo;
                    const leido = item.dataset.leido;

                    // Verificar búsqueda
                    const coincideBusqueda = !searchTerm || texto.includes(searchTerm);

                    // Verificar filtro
                    let coincideFiltro = true;
                    switch (currentFilter) {
                        case 'no-leidas':
                            coincideFiltro = leido === '0';
                            break;
                        case 'urgentes':
                            coincideFiltro = tipo === '1';
                            break;
                        case 'advertencias':
                            coincideFiltro = tipo === '2';
                            break;
                        case 'informativas':
                            coincideFiltro = tipo === '3';
                            break;
                        case 'todas':
                        default:
                            coincideFiltro = true;
                    }

                    // Mostrar/ocultar
                    if (coincideBusqueda && coincideFiltro) {
                        item.style.display = 'flex';
                        visibleCount++;

                        // Contar para estadísticas
                        if (leido === '0') unreadCount++;
                        if (tipo === '1') urgentCount++;
                        if (tipo === '2') warningCount++;
                        if (tipo === '3') infoCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Actualizar contadores
                actualizarContadores(unreadCount, urgentCount, warningCount, infoCount);

                // Mostrar mensaje si no hay resultados
                mostrarMensajeNoResultados(visibleCount === 0 && items.length > 0);
            }

            function actualizarContadores(unread, urgent, warning, info) {
                document.getElementById('unreadCount').textContent = unread;
                document.getElementById('urgentCount').textContent = urgent;
                document.getElementById('warningCount').textContent = warning;
                document.getElementById('infoCount').textContent = info;
            }

            function mostrarMensajeNoResultados(mostrar) {
                let mensaje = document.getElementById('noResultsMessage');
                const list = document.getElementById('notificationsList');

                if (mostrar && !mensaje) {
                    mensaje = document.createElement('div');
                    mensaje.id = 'noResultsMessage';
                    mensaje.className = 'empty-state';
                    mensaje.innerHTML = `
                <div class="empty-icon">
                    <i data-lucide="search-x"></i>
                </div>
                <h3>No se encontraron notificaciones</h3>
                <p>Intenta con otros términos de búsqueda o cambia el filtro.</p>
            `;
                    list.appendChild(mensaje);
                } else if (!mostrar && mensaje) {
                    mensaje.remove();
                }
            }

            function limpiarFiltros() {
                document.getElementById('searchInput').value = '';
                filtrarPorTipo('todas');
            }

            // ========== FUNCIONES DE GESTIÓN DE NOTIFICACIONES ==========

            // Función para marcar como leída (llamada desde onclick)
            async function marcarComoLeida(idNotif, btnElement) {
                try {
                    const response = await fetch('?action=notificaciones&method=leerNotif', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_notif=${idNotif}&id_usuario=${currentUserId}`
                    });

                    const result = await response.json();

                    if (result.success) {
                        const index = notificacionesData.findIndex(n => n.id_notif == idNotif);
                        if (index !== -1) {
                            notificacionesData[index].leido = true;
                        }
                        // Actualizar UI
                        const item = btnElement.closest('.notification-item');
                        if (item) {
                            item.classList.remove('unread');
                            item.dataset.leido = '1';

                            // Cambiar botón y circulo morao
                            btnElement.remove();

                            const unreadDot = item.querySelector('.unread-dot');
                            if (unreadDot) {
                                console.log('Eliminando círculo morado...');
                                unreadDot.remove();
                            }

                            // Actualizar contadores
                            aplicarFiltros();
                        }
                    } else {
                        throw new Error(result.message || 'Error desconocido');
                    }
                } catch (error) {
                    Swal.fire('Error', 'No se pudo marcar como leída', 'error');
                    console.error('Error:', error);
                }
            }

            // Función para marcar todas como leídas (llamada desde onclick)
            async function marcarTodasComoLeidas() {
                const result = await Swal.fire({
                    title: '¿Marcar todas como leídas?',
                    text: "Todas las notificaciones se marcarán como revisadas.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3F51B5',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, marcar todas',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch('?action=notificaciones&method=leerTodas', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_usuario=${currentUserId}`
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Actualizar todas las notificaciones en UI y sus circulos moraos
                            notificacionesData.forEach(notif => {
                                notif.leido = 1;
                            });
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.classList.remove('unread');
                                item.dataset.leido = '1';

                                const unreadDot = item.querySelector('.unread-dot');
                                if (unreadDot) {
                                    unreadDot.remove();
                                }
                                // Cambiar botones
                                const btn = item.querySelector('.mark-read');
                                if (btn) {
                                    btn.remove()
                                }
                            });

                            aplicarFiltros();

                            Swal.fire(
                                '¡Hecho!',
                                'Todas las notificaciones han sido marcadas como leídas.',
                                'success'
                            );
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudieron marcar todas como leídas', 'error');
                    }
                }
            }

            // Función para eliminar notificación (llamada desde onclick)
            async function eliminarNotificacion(idNotif, btnElement) {
                const result = await Swal.fire({
                    title: '¿Eliminar esta notificación?',
                    text: "Esta acción solo la eliminará para ti.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3F51B5',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch('?action=notificaciones&method=eliminarNotif', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_notif=${idNotif}&id_usuario=${currentUserId}`
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Eliminar elemento del DOM
                            const item = btnElement.closest('.notification-item');
                            if (item) {
                                item.style.opacity = '0';
                                item.style.transform = 'translateX(100px)';
                                setTimeout(() => item.remove(), 300);
                            }

                            aplicarFiltros();

                            Swal.fire(
                                'Eliminada',
                                'La notificación ha sido eliminada.',
                                'success'
                            );
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar la notificación', 'error');
                    }
                }
            }

            // Función para limpiar notificaciones leídas (llamada desde onclick)
            async function limpiarNotificaciones() {
                const result = await Swal.fire({
                    title: '¿Limpiar notificaciones leídas?',
                    text: "Se eliminarán todas las notificaciones marcadas como leídas.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3F51B5',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, limpiar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch('?action=notificaciones&method=limpiarLeidas', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_usuario=${currentUserId}`
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Eliminar elementos leídos del DOM
                            document.querySelectorAll('.notification-item[data-leido="1"]').forEach(item => {
                                item.style.opacity = '0';
                                item.style.transform = 'translateX(100px)';
                                setTimeout(() => item.remove(), 300);
                                console.log("hecho sin problema")
                            });

                            aplicarFiltros();
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire('Error', `No se pudieron limpiar las notificaciones: ${error}`, 'error');
                    }
                }
            }

            // ========== FUNCIÓN VER DETALLES ==========
            function verDetalles(notifParam) {
                const notif = notificacionesData.find(n => n.id_notif == notifParam.id_notif) || notifParam;
                // Marcar como leída si no lo está
                if (!notif.leido) {
                    // Encontrar y hacer clic en el botón de marcar como leída
                    const markReadBtn = document.querySelector(`.notification-item[data-id="${notif.id_notif}"] .mark-read`);
                    if (markReadBtn) {
                        // Simular clic
                        marcarComoLeida(notif.id_notif, markReadBtn);
                        
                        // Actualizar el objeto local inmediatamente
                        const index = notificacionesData.findIndex(n => n.id_notif == notif.id_notif);
                        if (index !== -1) {
                            notificacionesData[index].leido = true;
                        }
                    }
                }
                // Determinar tipo
                let tipoTexto, icono, color;
                switch (notif.tipo) {
                    case 1:
                        tipoTexto = 'Importante';
                        icono = 'alert-octagon';
                        color = '#F44336';
                        break;
                    case 2:
                        tipoTexto = 'Aviso';
                        icono = 'alert-triangle';
                        color = '#FF9800';
                        break;
                    case 3:
                        tipoTexto = 'Información';
                        icono = 'info';
                        color = '#2196F3';
                        break;
                    case 4:
                        tipoTexto = 'Éxito';
                        icono = 'check-circle';
                        color = '#4CAF50';
                        break;
                    default:
                        tipoTexto = 'Informativa';
                        icono = 'info';
                        color = '#2196F3';
                }
                
                const destinatarios = notif.usuarios || [];
                const esParaMi = destinatarios.some(u => u.id_usuario == currentUserId);

                let destinatariosHTML = '';
                if (destinatarios.length > 0) {
                    destinatariosHTML = `
                <div style="margin-top: 1.5rem;">
                    <p style="font-weight: 500; margin-bottom: 0.75rem; color: var(--gray-700);">👥 Destinatarios (${destinatarios.length})</p>
                    <div style="max-height: 200px; overflow-y: auto;">
                        ${destinatarios.map(user => `
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-sm); margin-bottom: 0.5rem;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: ${color}20; color: ${color}; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    ${(user.nombre || 'U').charAt(0).toUpperCase()}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 500; color: var(--gray-900);">
                                        ${user.nombre || 'Usuario'} 
                                        ${user.id_usuario == currentUserId ? '<span style="color: var(--primary-color); font-weight: 600;">(Tú)</span>' : ''}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--gray-600);">
                                        ${user.rol || 'Usuario'}
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
                }

                const modalContent = document.getElementById('modalContent');
                modalContent.innerHTML = `
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: ${color}20; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="${icono}" style="color: ${color}; width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.25rem 0; color: ${color};">${tipoTexto}</h4>
                            <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">para ti, ${currentName}</p>
                        </div>
                    </div>
                    <button onclick="cerrarModal()" style="background: none; border: none; cursor: pointer; color: var(--gray-400);">
                        <i data-lucide="x" style="width: 24px; height: 24px;"></i>
                    </button>
                </div>
                
                <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                    <p style="margin: 0; font-size: 1.1rem; line-height: 1.5;">${notif.mensaje}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <p style="font-weight: 500; margin-bottom: 0.5rem; color: var(--gray-700);">📅 Fecha</p>
                        <p style="margin: 0; color: var(--gray-600);">
                            ${new Date(notif.fecha_notif).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })}
                        </p>
                    </div>
                    
                    <div>
                        <p style="font-weight: 500; margin-bottom: 0.5rem; color: var(--gray-700);">👤 Destinatarios</p>
                        <p style="margin: 0; color: var(--gray-600);">${destinatarios.length} usuario(s)</p>
                    </div>
                </div>
                
                ${destinatariosHTML}
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
            <button onclick="eliminarNotificacion(${notif.id_notif}, document.querySelector('.notification-item[data-id=\\'${notif.id_notif}\\'] .delete')); cerrarModal();" 
                    style="flex: 1; padding: 0.75rem; background: #FFEBEE; color: var(--danger-color); border: none; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i data-lucide="trash-2"></i> Eliminar
            </button>
        </div>
        `;

                document.getElementById('detailsModal').classList.add('active');
                lucide.createIcons();
            }

            // Función para cerrar modal
            function cerrarModal() {
                document.getElementById('detailsModal').classList.remove('active');
            }

            // Función para recargar notificaciones
            function recargarNotificaciones() {
                location.reload();
            }

            // ========== FUNCIONES AUXILIARES ==========

            function obtenerTiempoRelativo(fecha) {
                if (!fecha) return '';

                const ahora = new Date();
                const fechaNotif = new Date(fecha);

                // Verificar si la fecha es válida
                if (isNaN(fechaNotif.getTime())) {
                    return 'Fecha inválida';
                }

                const diffMs = ahora - fechaNotif;
                const diffMin = Math.floor(diffMs / 60000);
                const diffHoras = Math.floor(diffMs / 3600000);
                const diffDias = Math.floor(diffMs / 86400000);

                if (diffMin < 1) return 'Ahora mismo';
                if (diffMin < 60) return `Hace ${diffMin} min`;
                if (diffHoras < 24) return `Hace ${diffHoras} h`;
                if (diffDias < 7) return `Hace ${diffDias} d`;

                // Si es más de una semana, mostrar fecha completa
                return fechaNotif.toLocaleDateString('es-ES', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            }

            // ========== INICIALIZACIÓN ==========
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar filtros
                aplicarFiltros();

                // Configurar eventos del modal
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        cerrarModal();
                    }
                });

                document.getElementById('detailsModal').addEventListener('click', function (e) {
                    if (e.target === this) {
                        cerrarModal();
                    }
                });

                // Configurar eventos de búsqueda
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', aplicarFiltros);
                }

                // Debug: Verificar que todo está funcionando
                console.log('Sistema de notificaciones cargado');
                console.log('Usuario actual ID:', currentUserId);
                console.log('Notificaciones cargadas:', document.querySelectorAll('.notification-item').length);

                // Verificar que los círculos morados existen
                const unreadDots = document.querySelectorAll('.unread-dot');
                console.log('Círculos morados encontrados:', unreadDots.length);
                // Mostrar mensaje si no hay notificaciones
                const items = document.querySelectorAll('.notification-item');
                if (items.length === 0) {
                    console.log('No hay notificaciones para mostrar');
                }
            });

            // Función para debugging (opcional)
            function debugFunciones() {
                console.log('=== FUNCIONES DISPONIBLES ===');
                console.log('1. filtrarPorTipo(tipo) - Filtra notificaciones por tipo');
                console.log('2. filtrarNotificaciones() - Filtra por texto de búsqueda');
                console.log('3. marcarComoLeida(id, btn) - Marca como leída');
                console.log('4. marcarComoNoLeida(id, btn) - Marca como no leída');
                console.log('5. marcarTodasComoLeidas() - Marca todas como leídas');
                console.log('6. eliminarNotificacion(id, btn) - Elimina notificación');
                console.log('7. limpiarNotificaciones() - Limpia notificaciones leídas');
                console.log('8. verDetalles(notif) - Muestra detalles de notificación');
                console.log('9. cerrarModal() - Cierra modal de detalles');
                console.log('10. recargarNotificaciones() - Recarga la página');
                console.log('11. obtenerTiempoRelativo(fecha) - Calcula tiempo relativo');
                console.log('12. aplicarFiltros() - Aplica filtros actuales');
                console.log('13. actualizarContadores() - Actualiza contadores de estadísticas');
                console.log('14. mostrarMensajeNoResultados() - Muestra mensaje si no hay resultados');
                console.log('15. limpiarFiltros() - Limpia todos los filtros');
            }

            // Llamar a debug si se necesita
            // debugFunciones();
        </script>
</body>

</html>