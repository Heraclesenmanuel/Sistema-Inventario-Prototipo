<?php
// Controlador debería pasar estos datos:
// $solicitudesRevision - Array con solicitudes EN REVISIÓN para presupuesto
// $proveedores - Array de proveedores disponibles
// $tiposProducto - Array de tipos de producto
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Sistema' ?> - Gestión de Presupuesto</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="public/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/movimientos.css">
    
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
            <!-- Panel Principal -->
            <div class="main-panel">
                <!-- Header -->
                <div class="panel-header">
                    <div class="header-content">
                        <div class="header-title">
                            <h1>
                                <i data-lucide="calculator"></i>
                                Gestión de Presupuesto
                                <?php if(count($solicitudesRevision ?? []) > 0): ?>
                                    <span class="badge"><?= count($solicitudesRevision) ?> en revisión</span>
                                <?php endif; ?>
                            </h1>
                            <p>Asignación de proveedores y presupuesto para solicitudes en revisión</p>
                        </div>
                        <button class="btn btn-primary" onclick="exportarReporte()">
                            <i data-lucide="download"></i>
                            Exportar Reporte
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters-section">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Prioridad</label>
                            <select class="filter-select" id="filterPrioridad" onchange="filtrarSolicitudes()">
                                <option value="todos">Todas las prioridades</option>
                                <option value="alta">Alta prioridad</option>
                                <option value="media">Prioridad media</option>
                                <option value="baja">Baja prioridad</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Oficina</label>
                            <select class="filter-select" id="filterOficina" onchange="filtrarSolicitudes()">
                                <option value="todos">Todas las oficinas</option>
                                <?php foreach($oficinas['data'] as $oficina): ?>
                                    <option value="<?= $oficina['num_oficina'] ?>">
                                        <?= htmlspecialchars($oficina['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Tipo de Producto</label>
                            <select class="filter-select" id="filterTipo" onchange="filtrarSolicitudes()">
                                <option value="todos">Todos los tipos</option>
                                <?php foreach($tiposProducto['data'] ?? [] as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo'] ?>">
                                        <?= htmlspecialchars($tipo['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="btn btn-secondary" onclick="limpiarFiltros()">
                                <i data-lucide="filter-x"></i>
                                Limpiar filtros
                            </button>
                            <button class="btn btn-warning" onclick="validarTodasAsignaciones()">
                                <i data-lucide="check-circle"></i>
                                Validar todas
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contenido Principal -->
                <div class="content-section">
                    <!-- Estadísticas -->
                    <div class="stats-row">
                        <?php
                        // Calcular estadísticas
                        $totalSolicitudes = count($solicitudesRevision ?? []);
                        $totalProductos = 0;
                        $totalMonto = 0;
                        $sinProveedor = 0;
                        $urgentes = 0;
                        
                        foreach($solicitudesRevision ?? [] as $solicitud) {
                            $hoy = new DateTime();
                            $fechaDeseo = new DateTime($solicitud['fecha_deseo']);
                            $diasDiferencia = $hoy->diff($fechaDeseo)->days;
                            
                            if($diasDiferencia <= 3) $urgentes++;
                            
                            foreach($solicitud['productos'] ?? [] as $producto) {
                                $totalProductos++;
                                if(empty($producto['rif_proveedor'])) $sinProveedor++;
                                $totalMonto += ($producto['precio_unitario'] ?? 0) * ($producto['un_deseadas'] ?? 0);
                            }
                        }
                        ?>
                        
                        <div class="stat-card" onclick="filtrarPorPrioridad('alta')">
                            <div class="stat-icon urgent">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="countUrgentes"><?= $urgentes ?></h3>
                                <p>Urgentes (≤ 3 días)</p>
                            </div>
                        </div>
                        
                        <div class="stat-card" onclick="filtrarSinProveedor()">
                            <div class="stat-icon revision">
                                <i data-lucide="package"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="countSinProveedor"><?= $sinProveedor ?></h3>
                                <p>Productos sin proveedor</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon proveedores">
                                <i data-lucide="building"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="countProveedores"><?= count($proveedores ?? []) ?></h3>
                                <p>Proveedores disponibles</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon monto">
                                <i data-lucide="dollar-sign"></i>
                            </div>
                            <div class="stat-info">
                                <h3>$<span id="totalMonto"><?= number_format($totalMonto, 2) ?></span></h3>
                                <p>Monto total estimado</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Solicitudes EN REVISIÓN -->
                    <div class="solicitudes-container" id="solicitudesList">
                        <?php if(!empty($solicitudes)): ?>
                            <?php foreach($solicitudes as $solicitud): ?>
                                <?php 
                                $productos = $solicitud['productos'] ?? [];
                                
                                // Calcular prioridad basada en fecha deseada
                                $hoy = new DateTime();
                                $fechaDeseo = new DateTime($solicitud['fecha_deseo']);
                                $diasDiferencia = $hoy->diff($fechaDeseo)->days;
                                
                                if($diasDiferencia <= 3) {
                                    $prioridad = 'alta';
                                    $prioridadText = 'Alta';
                                    $prioridadClass = 'priority-high';
                                } elseif($diasDiferencia <= 7) {
                                    $prioridad = 'media';
                                    $prioridadText = 'Media';
                                    $prioridadClass = 'priority-medium';
                                } else {
                                    $prioridad = 'baja';
                                    $prioridadText = 'Baja';
                                    $prioridadClass = 'priority-low';
                                }
                                
                                // Contar productos sin proveedor
                                $sinProveedor = 0;
                                $totalProductos = count($productos);
                                $montoTotal = 0;
                                
                                foreach($productos as $producto) {
                                    if(empty($producto['rif_proveedor'])) $sinProveedor++;
                                    $montoTotal += ($producto['precio_unitario'] ?? 0) * ($producto['un_deseadas'] ?? 0);
                                }
                                ?>
                                
                                <div class="solicitud-card" 
                                     data-id="<?= $solicitud['id_solicitud'] ?>"
                                     data-prioridad="<?= $prioridad ?>"
                                     data-oficina="<?= $solicitud['num_oficina'] ?>"
                                     data-tipos="<?= implode(',', array_column($productos, 'id_tipo')) ?>"
                                     data-sin-proveedor="<?= $sinProveedor ?>">
                                    
                                    <div class="solicitud-header">
                                        <div class="solicitud-info">
                                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                                <h3>Solicitud #<?= $solicitud['id_solicitud'] ?></h3>
                                                <span class="solicitud-priority <?= $prioridadClass ?>">
                                                    <i data-lucide="clock"></i> <?= $prioridadText ?>
                                                </span>
                                                <?php if($sinProveedor > 0): ?>
                                                    <span class="badge" style="background: var(--warning-color); color: white;">
                                                        <i data-lucide="alert-circle"></i> <?= $sinProveedor ?> sin proveedor
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="solicitud-meta">
                                                <div class="meta-item">
                                                    <i data-lucide="user"></i>
                                                    <span><?= htmlspecialchars($solicitud['nombre_solicitante'] ?? 'Usuario') ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i data-lucide="building"></i>
                                                    <span>Oficina <?= htmlspecialchars($solicitud['nombre_oficina']) ?> (<?= $solicitud['num_oficina'] ?>)</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i data-lucide="calendar"></i>
                                                    <span>Solicitado: <?= date('d/m/Y', strtotime($solicitud['fecha_solic'])) ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i data-lucide="clock"></i>
                                                    <span>Entrega deseada: <?= date('d/m/Y', strtotime($solicitud['fecha_deseo'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="solicitud-status status-revision">
                                            <i data-lucide="search"></i> En Revisión
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($solicitud['comentarios'])): ?>
                                        <div style="margin: 1rem 0; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-md);">
                                            <p style="margin: 0; color: var(--gray-700); font-style: italic;">
                                                <i data-lucide="message-square" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                                                <?= htmlspecialchars($solicitud['comentarios']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Resumen de asignación -->
                                    <div class="asignacion-summary">
                                        <div class="summary-header">
                                            <h4 style="font-weight: 600; color: var(--review-color);">
                                                <i data-lucide="clipboard-check"></i> Resumen de asignación
                                            </h4>
                                            <span class="badge"><?= $totalProductos ?> productos</span>
                                        </div>
                                        <div class="summary-content">
                                            <div class="summary-item">
                                                <h4>Productos sin proveedor</h4>
                                                <p style="color: <?= $sinProveedor > 0 ? 'var(--warning-color)' : 'var(--success-color)' ?>;">
                                                    <?= $sinProveedor ?> de <?= $totalProductos ?>
                                                </p>
                                            </div>
                                            <div class="summary-item">
                                                <h4>Monto total estimado</h4>
                                                <p>$<?= number_format($montoTotal, 2) ?></p>
                                            </div>
                                            <div class="summary-item">
                                                <h4>Días para entrega</h4>
                                                <p style="color: <?= $diasDiferencia <= 3 ? 'var(--danger-color)' : ($diasDiferencia <= 7 ? 'var(--warning-color)' : 'var(--success-color)') ?>;">
                                                    <?= $diasDiferencia ?> días
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Productos solicitados -->
                                    <?php if(!empty($productos)): ?>
                                        <div class="productos-grid">
                                            <?php foreach($productos as $producto): ?>
                                                <?php 
                                                $proveedoresTipo = $proveedoresPorTipo[$producto['id_tipo']] ?? [];
                                                $tieneProveedor = !empty($producto['rif_proveedor']);
                                                ?>
                                                <div class="producto-item" data-tipo="<?= $producto['id_tipo'] ?>">
                                                    <div class="producto-header">
                                                        <div>
                                                            <span class="producto-name"><?= htmlspecialchars($producto['nombre']) ?></span>
                                                            <div class="producto-details">
                                                                <span>Tipo: <?= htmlspecialchars($producto['tipo_nombre'] ?? 'N/A') ?></span>
                                                            </div>
                                                        </div>
                                                        <span class="producto-quantity"><?= $producto['un_deseadas'] ?> <?= htmlspecialchars($producto['medida']) ?></span>
                                                    </div>
                                                    
                                                    <!-- Asignación de proveedor -->
                                                    <div class="proveedor-asignacion">
                                                        <div class="asignacion-form" data-linea="<?= $producto['num_linea'] ?>">
                                                            <div class="form-group">
                                                                <label class="form-label">Proveedor</label>
                                                                <div style="display: flex; gap: 0.5rem;">
                                                                    <select class="form-select proveedor-select" 
                                                                            data-tipo="<?= $producto['id_tipo'] ?>"
                                                                            onchange="cambiarProveedor(<?= $solicitud['id_solicitud'] ?>, <?= $producto['num_linea'] ?>, this.value)">
                                                                        <option value="">Seleccionar proveedor</option>
                                                                        <?php foreach($proveedoresTipo as $prov): ?>
                                                                            <option value="<?= $prov['rif'] ?>" 
                                                                                    <?= ($producto['rif_proveedor'] ?? '') == $prov['rif'] ? 'selected' : '' ?>
                                                                                    data-info='<?= json_encode($prov) ?>'>
                                                                                <?= htmlspecialchars($prov['nombre']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <button class="btn btn-small btn-secondary" 
                                                                            onclick="buscarProveedor(<?= $producto['id_tipo'] ?>)"
                                                                            title="Buscar más proveedores">
                                                                        <i data-lucide="search"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label class="form-label">Precio unitario ($)</label>
                                                                <input type="number" 
                                                                       class="form-input precio-input"
                                                                       data-tipo="<?= $producto['id_tipo'] ?>"
                                                                       placeholder="0.00"
                                                                       step="0.01"
                                                                       min="0"
                                                                       value="<?= $producto['precio_unitario'] ?? '' ?>"
                                                                       onchange="actualizarPrecio(<?= $solicitud['id_solicitud'] ?>, <?= $producto['num_linea'] ?>, this.value)"
                                                                       <?= !$tieneProveedor ? 'disabled' : '' ?>>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label class="form-label">Subtotal</label>
                                                                <div class="form-input" style="background: var(--gray-100); font-weight: 600;">
                                                                    $<span id="subtotal_<?= $solicitud['id_solicitud'] ?>_<?= $producto['num_linea'] ?>">
                                                                        <?= number_format(($producto['precio_unitario'] ?? 0) * ($producto['un_deseadas'] ?? 0), 2) ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if($tieneProveedor && isset($producto['nombre_proveedor'])): ?>
                                                            <div style="margin-top: 0.5rem; padding: 0.75rem; background: #E8F5E9; border-radius: var(--radius-sm); border-left: 4px solid var(--success-color);">
                                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                    <i data-lucide="check-circle" style="color: var(--success-color);"></i>
                                                                    <span><strong>Asignado:</strong> <?= htmlspecialchars($producto['nombre_proveedor']) ?></span>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Acciones -->
                                    <div class="solicitud-actions">
                                        <div style="display: flex; gap: 1rem; align-items: center;">
                                            <span style="color: var(--gray-600); font-size: 0.875rem;">
                                                <i data-lucide="info"></i> Asigne proveedores a todos los productos antes de aprobar
                                            </span>
                                        </div>
                                        <div style="display: flex; gap: 1rem;">
                                            <button class="btn btn-secondary" onclick="solicitarInfoAdicional(<?= $solicitud['id_solicitud'] ?>)">
                                                <i data-lucide="message-circle"></i>
                                                Solicitar info
                                            </button>
                                            <button class="btn btn-danger" onclick="rechazarSolicitud(<?= $solicitud['id_solicitud'] ?>)">
                                                <i data-lucide="x-circle"></i>
                                                Rechazar
                                            </button>
                                            <button class="btn btn-success" onclick="aprobarSolicitud(<?= $solicitud['id_solicitud'] ?>)">
                                                <i data-lucide="check-circle"></i>
                                                Aprobar con presupuesto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i data-lucide="check-circle"></i>
                                </div>
                                <h3>No hay solicitudes en revisión</h3>
                                <p>Todas las solicitudes han sido gestionadas o no hay solicitudes pendientes de presupuesto.</p>
                                <button class="btn btn-primary" onclick="location.reload()">
                                    <i data-lucide="refresh-cw"></i>
                                    Recargar
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para detalles de proveedor -->
        <div class="modal-overlay" id="proveedorModal">
            <div class="modal-content" id="proveedorModalContent">
                <!-- Contenido dinámico -->
            </div>
        </div>
        
        <!-- Modal para solicitar información adicional -->
        <div class="modal-overlay" id="infoModal">
            <div class="modal-content" style="max-width: 500px;">
                <h2 style="margin-bottom: 1rem;"><i data-lucide="message-circle"></i> Solicitar Información Adicional</h2>
                <div class="form-group">
                    <label class="form-label">Solicitud información sobre:</label>
                    <select class="form-select" id="tipoInfo">
                        <option value="producto">Especificaciones del producto</option>
                        <option value="cantidad">Justificación de cantidad</option>
                        <option value="proveedor">Información de proveedor alternativo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mensaje:</label>
                    <textarea class="form-input" id="mensajeInfo" rows="4" placeholder="Describa qué información adicional necesita..."></textarea>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                    <button class="btn btn-secondary" onclick="cerrarModalInfo()">Cancelar</button>
                    <button class="btn btn-primary" onclick="enviarSolicitudInfo()">Enviar solicitud</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inicializar Iconos
        lucide.createIcons();

        // Variables globales
        let asignacionesProveedor = {};
        let preciosUnitarios = {};
        let solicitudInfoActual = null;

        // ========== FUNCIONES DE FILTRADO ==========
        
        function filtrarSolicitudes() {
            const prioridad = document.getElementById('filterPrioridad').value;
            const oficina = document.getElementById('filterOficina').value;
            const tipo = document.getElementById('filterTipo').value;
            
            const solicitudes = document.querySelectorAll('.solicitud-card');
            let visibleCount = 0;
            let urgentesCount = 0;
            let sinProveedorCount = 0;
            
            solicitudes.forEach(solicitud => {
                const solicitudPrioridad = solicitud.dataset.prioridad;
                const solicitudOficina = solicitud.dataset.oficina;
                const solicitudTipos = solicitud.dataset.tipos.split(',');
                const sinProveedor = parseInt(solicitud.dataset.sinProveedor) || 0;
                
                const coincidePrioridad = prioridad === 'todos' || solicitudPrioridad === prioridad;
                const coincideOficina = oficina === 'todos' || solicitudOficina === oficina;
                const coincideTipo = tipo === 'todos' || solicitudTipos.includes(tipo);
                
                if (coincidePrioridad && coincideOficina && coincideTipo) {
                    solicitud.style.display = 'block';
                    visibleCount++;
                    
                    // Contar para estadísticas
                    if (solicitudPrioridad === 'alta') urgentesCount++;
                    if (sinProveedor > 0) sinProveedorCount++;
                } else {
                    solicitud.style.display = 'none';
                }
            });
            
            // Actualizar contadores
            document.getElementById('countUrgentes').textContent = urgentesCount;
            document.getElementById('countSinProveedor').textContent = sinProveedorCount;
            
            // Mostrar mensaje si no hay resultados
            mostrarMensajeNoResultados(visibleCount === 0);
        }
        
        function filtrarPorPrioridad(prioridad) {
            document.getElementById('filterPrioridad').value = prioridad;
            filtrarSolicitudes();
        }
        
        function filtrarSinProveedor() {
            const solicitudes = document.querySelectorAll('.solicitud-card');
            let encontradas = false;
            
            solicitudes.forEach(solicitud => {
                const sinProveedor = parseInt(solicitud.dataset.sinProveedor) || 0;
                if (sinProveedor > 0) {
                    solicitud.style.display = 'block';
                    encontradas = true;
                    // Resaltar productos sin proveedor
                    const productos = solicitud.querySelectorAll('.producto-item');
                    productos.forEach(producto => {
                        const select = producto.querySelector('.proveedor-select');
                        if (select && !select.value) {
                            producto.style.boxShadow = '0 0 0 2px var(--warning-color)';
                        }
                    });
                } else {
                    solicitud.style.display = 'none';
                }
            });
            
            if (!encontradas) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Excelente!',
                    text: 'Todas las solicitudes tienen proveedores asignados.',
                    timer: 2000
                });
            }
        }
        
        function limpiarFiltros() {
            document.getElementById('filterPrioridad').value = 'todos';
            document.getElementById('filterOficina').value = 'todos';
            document.getElementById('filterTipo').value = 'todos';
            filtrarSolicitudes();
            
            // Quitar resaltados
            document.querySelectorAll('.producto-item').forEach(item => {
                item.style.boxShadow = '';
            });
        }
        
        function mostrarMensajeNoResultados(mostrar) {
            let mensaje = document.getElementById('noResultsMessage');
            const container = document.getElementById('solicitudesList');
            
            if (mostrar && !mensaje) {
                mensaje = document.createElement('div');
                mensaje.id = 'noResultsMessage';
                mensaje.className = 'empty-state';
                mensaje.innerHTML = `
                    <div class="empty-icon">
                        <i data-lucide="search-x"></i>
                    </div>
                    <h3>No se encontraron solicitudes</h3>
                    <p>Intenta con otros filtros o cambia los criterios de búsqueda.</p>
                    <button class="btn btn-primary" onclick="limpiarFiltros()">
                        Mostrar todas
                    </button>
                `;
                container.appendChild(mensaje);
            } else if (!mostrar && mensaje) {
                mensaje.remove();
            }
        }

        // ========== FUNCIONES DE ASIGNACIÓN DE PROVEEDORES ==========
        
        function cambiarProveedor(idSolicitud, numLinea, rifProveedor) {
            // Guardar asignación
            if (!asignacionesProveedor[idSolicitud]) {
                asignacionesProveedor[idSolicitud] = {};
            }
            asignacionesProveedor[idSolicitud][numLinea] = rifProveedor;
            
            // Habilitar campo de precio
            const precioInput = document.querySelector(`[data-linea="${numLinea}"] .precio-input`);
            if (precioInput) {
                precioInput.disabled = !rifProveedor;
                if (!rifProveedor) {
                    precioInput.value = '';
                    actualizarPrecio(idSolicitud, numLinea, 0);
                }
            }
            
            // Actualizar contador de productos sin proveedor
            actualizarContadorSinProveedor(idSolicitud);
            
            console.log(`Proveedor asignado: Solicitud ${idSolicitud}, Línea ${numLinea}, RIF ${rifProveedor}`);
        }
        
        function actualizarContadorSinProveedor(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;
            
            const selects = solicitudDiv.querySelectorAll('.proveedor-select');
            let sinProveedor = 0;
            
            selects.forEach(select => {
                if (!select.value) sinProveedor++;
            });
            
            solicitudDiv.dataset.sinProveedor = sinProveedor;
            
            // Actualizar badge
            let badge = solicitudDiv.querySelector('.badge');
            if (sinProveedor > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge';
                    badge.style.cssText = 'background: var(--warning-color); color: white; margin-left: 1rem;';
                    const headerDiv = solicitudDiv.querySelector('.solicitud-info h3');
                    if (headerDiv) {
                        headerDiv.appendChild(badge);
                    }
                }
                badge.innerHTML = `<i data-lucide="alert-circle"></i> ${sinProveedor} sin proveedor`;
                lucide.createIcons();
            } else if (badge) {
                badge.remove();
            }
        }
        
        function actualizarPrecio(idSolicitud, numLinea, precio) {
            // Validar precio
            precio = parseFloat(precio);
            if (isNaN(precio) || precio < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Precio inválido',
                    text: 'Ingrese un precio válido mayor o igual a 0'
                });
                return;
            }
            
            // Guardar precio
            if (!preciosUnitarios[idSolicitud]) {
                preciosUnitarios[idSolicitud] = {};
            }
            preciosUnitarios[idSolicitud][numLinea] = precio;
            
            // Recalcular subtotal
            recalcularSubtotal(idSolicitud, numLinea, precio);
            
            // Recalcular total del monto
            recalcularTotalMonto();
        }
        
        function recalcularSubtotal(idSolicitud, numLinea, precio) {
            // Buscar la cantidad de productos
            const productoDiv = document.querySelector(`[data-linea="${numLinea}"]`);
            if (!productoDiv) return;
            
            const cantidadSpan = productoDiv.closest('.producto-item').querySelector('.producto-quantity');
            const cantidadText = cantidadSpan.textContent;
            const cantidad = parseInt(cantidadText.split(' ')[0]);
            
            // Calcular subtotal
            const subtotal = precio * cantidad;
            
            // Actualizar display
            const subtotalSpan = document.getElementById(`subtotal_${idSolicitud}_${numLinea}`);
            if (subtotalSpan) {
                subtotalSpan.textContent = subtotal.toFixed(2);
            }
            
            // Recalcular total de la solicitud
            recalcularTotalSolicitud(idSolicitud);
        }
        
        function recalcularTotalSolicitud(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;
            
            let total = 0;
            const subtotalSpans = solicitudDiv.querySelectorAll('[id^="subtotal_"]');
            
            subtotalSpans.forEach(span => {
                total += parseFloat(span.textContent) || 0;
            });
            
            // Actualizar resumen
            const summaryItem = solicitudDiv.querySelector('.summary-content .summary-item:nth-child(2) p');
            if (summaryItem) {
                summaryItem.textContent = '$' + total.toFixed(2);
            }
        }
        
        function recalcularTotalMonto() {
            let totalMonto = 0;
            document.querySelectorAll('.solicitud-card').forEach(solicitud => {
                const subtotalSpans = solicitud.querySelectorAll('[id^="subtotal_"]');
                subtotalSpans.forEach(span => {
                    totalMonto += parseFloat(span.textContent) || 0;
                });
            });
            
            document.getElementById('totalMonto').textContent = totalMonto.toFixed(2);
        }

        // ========== FUNCIONES DE APROBACIÓN/RECHAZO ==========
        
        function validarTodasAsignaciones() {
            const solicitudes = document.querySelectorAll('.solicitud-card');
            let todasCompletas = true;
            let mensaje = '';
            
            solicitudes.forEach(solicitud => {
                const idSolicitud = solicitud.dataset.id;
                const selects = solicitud.querySelectorAll('.proveedor-select');
                let incompletas = 0;
                
                selects.forEach(select => {
                    if (!select.value) incompletas++;
                });
                
                if (incompletas > 0) {
                    todasCompletas = false;
                    mensaje += `• Solicitud #${idSolicitud}: ${incompletas} productos sin proveedor\n`;
                    
                    // Resaltar solicitud
                    solicitud.style.boxShadow = '0 0 0 3px var(--warning-color)';
                }
            });
            
            if (todasCompletas) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Validación exitosa!',
                    text: 'Todas las solicitudes tienen proveedores asignados.',
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Asignaciones incompletas',
                    html: `<div style="text-align: left;">${mensaje.replace(/\n/g, '<br>')}</div>`,
                    confirmButtonText: 'Entendido'
                });
            }
        }
        
        async function aprobarSolicitud(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;
            
            // Verificar que todos los productos tengan proveedor asignado
            const productos = solicitudDiv.querySelectorAll('.producto-item');
            let faltanProveedores = false;
            let faltanPrecios = false;
            let productosFaltantes = [];
            
            productos.forEach(producto => {
                const select = producto.querySelector('.proveedor-select');
                const precioInput = producto.querySelector('.precio-input');
                
                if (select && !select.value) {
                    faltanProveedores = true;
                    productosFaltantes.push({
                        nombre: producto.querySelector('.producto-name').textContent,
                        tipo: 'proveedor'
                    });
                }
                
                if (precioInput && (!precioInput.value || parseFloat(precioInput.value) <= 0)) {
                    faltanPrecios = true;
                    productosFaltantes.push({
                        nombre: producto.querySelector('.producto-name').textContent,
                        tipo: 'precio'
                    });
                }
            });
            
            if (faltanProveedores || faltanPrecios) {
                let mensaje = 'Antes de aprobar, complete la siguiente información:<br><br>';
                
                if (faltanProveedores) {
                    mensaje += '<strong>Productos sin proveedor:</strong><br>';
                    productosFaltantes.filter(p => p.tipo === 'proveedor').forEach(p => {
                        mensaje += `• ${p.nombre}<br>`;
                    });
                    mensaje += '<br>';
                }
                
                if (faltanPrecios) {
                    mensaje += '<strong>Productos sin precio:</strong><br>';
                    productosFaltantes.filter(p => p.tipo === 'precio').forEach(p => {
                        mensaje += `• ${p.nombre}<br>`;
                    });
                }
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Información incompleta',
                    html: mensaje,
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            // Confirmar aprobación
            Swal.fire({
                title: '¿Aprobar solicitud con presupuesto?',
                html: `
                    <div style="text-align: left;">
                        <p>Esta acción:</p>
                        <ul>
                            <li>Asignará los proveedores seleccionados</li>
                            <li>Registrará los precios unitarios</li>
                            <li>Cambiará el estado a "Aprobado"</li>
                            <li>Notificará al solicitante</li>
                        </ul>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4CAF50'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // Recolectar datos
                        const asignaciones = [];
                        productos.forEach(producto => {
                            const select = producto.querySelector('.proveedor-select');
                            const precioInput = producto.querySelector('.precio-input');
                            const linea = producto.querySelector('[data-linea]').dataset.linea;
                            const tipo = producto.dataset.tipo;
                            
                            if (select && precioInput) {
                                asignaciones.push({
                                    id_solicitud: idSolicitud,
                                    num_linea: linea,
                                    rif_proveedor: select.value,
                                    id_tipo: tipo,
                                    precio_unitario: parseFloat(precioInput.value)
                                });
                            }
                        });
                        
                        // Enviar al servidor
                        const response = await fetch('controller/PresupuestoController.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'aprobarSolicitud',
                                id_solicitud: idSolicitud,
                                asignaciones: asignaciones
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Solicitud aprobada!',
                                text: 'La solicitud ha sido aprobada con presupuesto.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Eliminar solicitud de la vista
                            setTimeout(() => {
                                solicitudDiv.remove();
                                
                                // Actualizar contadores
                                const totalSolicitudes = document.querySelectorAll('.solicitud-card').length;
                                if (totalSolicitudes === 0) {
                                    mostrarMensajeNoResultados(true);
                                }
                                
                                recalcularTotalMonto();
                            }, 2000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Ocurrió un error al aprobar la solicitud.'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo procesar la solicitud.'
                        });
                    }
                }
            });
        }
        
        async function rechazarSolicitud(idSolicitud) {
            const { value: motivo } = await Swal.fire({
                title: 'Motivo del rechazo',
                input: 'textarea',
                inputLabel: 'Por favor, especifique el motivo del rechazo:',
                inputPlaceholder: 'Ej: Precios fuera de presupuesto, proveedores no disponibles...',
                inputAttributes: {
                    'aria-label': 'Motivo del rechazo'
                },
                showCancelButton: true,
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#F44336',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe especificar un motivo para el rechazo';
                    }
                    if (value.length < 10) {
                        return 'El motivo debe tener al menos 10 caracteres';
                    }
                }
            });
            
            if (motivo) {
                try {
                    const response = await fetch('controller/PresupuestoController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'rechazarSolicitud',
                            id_solicitud: idSolicitud,
                            motivo: motivo
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Solicitud rechazada',
                            text: 'La solicitud ha sido rechazada.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Eliminar solicitud de la vista
                        const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
                        if (solicitudDiv) {
                            solicitudDiv.remove();
                            
                            // Actualizar contadores
                            const totalSolicitudes = document.querySelectorAll('.solicitud-card').length;
                            if (totalSolicitudes === 0) {
                                mostrarMensajeNoResultados(true);
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Ocurrió un error al rechazar la solicitud.'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo procesar la solicitud.'
                    });
                }
            }
        }
        
        // ========== FUNCIONES DE SOLICITUD DE INFORMACIÓN ==========
        
        function solicitarInfoAdicional(idSolicitud) {
            solicitudInfoActual = idSolicitud;
            document.getElementById('infoModal').classList.add('active');
        }
        
        function cerrarModalInfo() {
            document.getElementById('infoModal').classList.remove('active');
            solicitudInfoActual = null;
        }
        
        async function enviarSolicitudInfo() {
            const tipoInfo = document.getElementById('tipoInfo').value;
            const mensaje = document.getElementById('mensajeInfo').value;
            
            if (!mensaje) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mensaje requerido',
                    text: 'Por favor ingrese un mensaje para la solicitud de información.'
                });
                return;
            }
            
            try {
                const response = await fetch('controller/PresupuestoController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'solicitarInfo',
                        id_solicitud: solicitudInfoActual,
                        tipo_info: tipoInfo,
                        mensaje: mensaje
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Solicitud enviada',
                        text: 'La solicitud de información ha sido enviada al usuario.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    cerrarModalInfo();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo enviar la solicitud.'
                });
            }
        }

        // ========== FUNCIONES DE PROVEEDORES ==========
        
        async function buscarProveedor(idTipo) {
            try {
                const response = await fetch(`controller/ProveedorController.php?action=getByTipo&id_tipo=${idTipo}`);
                const proveedores = await response.json();
                
                if (!proveedores || proveedores.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No hay proveedores',
                        text: 'No hay proveedores registrados para este tipo de producto.'
                    });
                    return;
                }
                
                const modalContent = document.getElementById('proveedorModalContent');
                modalContent.innerHTML = `
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">
                            <i data-lucide="building"></i> Proveedores disponibles
                        </h2>
                        <p style="color: var(--gray-600);">Seleccione un proveedor para asignar:</p>
                    </div>
                    
                    <div style="display: grid; gap: 1rem; max-height: 400px; overflow-y: auto;">
                        ${proveedores.map(proveedor => `
                            <div style="padding: 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                    <div>
                                        <h4 style="margin: 0 0 0.5rem 0;">${proveedor.nombre}</h4>
                                        <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">
                                            RIF: ${proveedor.rif} | Tel: ${proveedor.telefono}
                                        </p>
                                        <p style="margin: 0.5rem 0 0 0; color: var(--gray-600); font-size: 0.875rem;">
                                            Email: ${proveedor.email}
                                        </p>
                                    </div>
                                    <button class="btn btn-small btn-primary" onclick="seleccionarProveedorModal('${proveedor.rif}', '${proveedor.nombre}', ${idTipo})">
                                        Seleccionar
                                    </button>
                                </div>
                                <div style="display: flex; gap: 1rem; font-size: 0.875rem;">
                                    <span style="color: var(--gray-600);">Estado: ${proveedor.estado}</span>
                                    <span style="color: var(--gray-600);">Nota: ${proveedor.nota || 'Sin notas'}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
                
                document.getElementById('proveedorModal').classList.add('active');
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los proveedores.'
                });
            }
        }
        
        function seleccionarProveedorModal(rif, nombre, idTipo) {
            // Encontrar todos los selects del tipo correspondiente
            const selects = document.querySelectorAll(`.proveedor-select[data-tipo="${idTipo}"]:not([value])`);
            
            if (selects.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Todos asignados',
                    text: 'Todos los productos de este tipo ya tienen proveedor asignado.'
                });
                return;
            }
            
            // Asignar a todos los selects del tipo que estén vacíos
            selects.forEach(select => {
                select.value = rif;
                
                // Disparar evento change
                const event = new Event('change');
                select.dispatchEvent(event);
            });
            
            document.getElementById('proveedorModal').classList.remove('active');
            
            Swal.fire({
                icon: 'success',
                title: 'Proveedor asignado',
                text: `Proveedor "${nombre}" asignado a todos los productos del tipo.`,
                timer: 1500
            });
        }
        
        function exportarReporte() {
            Swal.fire({
                title: 'Exportar reporte de presupuesto',
                text: '¿En qué formato desea exportar el reporte?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Excel',
                cancelButtonText: 'PDF',
                showDenyButton: true,
                denyButtonText: 'Cancelar',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#2196F3'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Exportar a Excel
                    window.location.href = 'controller/ReporteController.php?action=excel&rol=presupuesto&estado=revision';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Exportar a PDF
                    window.location.href = 'controller/ReporteController.php?action=pdf&rol=presupuesto&estado=revision';
                }
            });
        }

        // ========== INICIALIZACIÓN ==========
        
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar filtros
            filtrarSolicitudes();
            
            // Cargar asignaciones existentes
            document.querySelectorAll('.proveedor-select[selected]').forEach(select => {
                const solicitudId = select.closest('.solicitud-card').dataset.id;
                const numLinea = select.closest('[data-linea]').dataset.linea;
                const rifProveedor = select.value;
                
                if (solicitudId && numLinea && rifProveedor) {
                    cambiarProveedor(solicitudId, numLinea, rifProveedor);
                }
            });
            
            // Cargar precios existentes
            document.querySelectorAll('.precio-input').forEach(input => {
                if (input.value) {
                    const solicitudId = input.closest('.solicitud-card').dataset.id;
                    const numLinea = input.closest('[data-linea]').dataset.linea;
                    const precio = parseFloat(input.value);
                    
                    if (solicitudId && numLinea && !isNaN(precio)) {
                        actualizarPrecio(solicitudId, numLinea, precio);
                    }
                }
            });
        });

        // ========== EVENT LISTENERS ==========
        
        // Cerrar modales al hacer clic fuera
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    if (this.id === 'infoModal') {
                        solicitudInfoActual = null;
                    }
                }
            });
        });
        
        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    modal.classList.remove('active');
                });
                solicitudInfoActual = null;
            }
        });
        
        // Auto-guardar cambios cada 30 segundos
        setInterval(() => {
            console.log('Auto-guardando asignaciones...');
            // Aquí podrías implementar auto-guardado si es necesario
        }, 30000);
    </script>
</body>
</html>