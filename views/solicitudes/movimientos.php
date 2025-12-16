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
                                <?php if (count($solicitudes['data'] ?? []) > 0): ?>
                                    <span class="badge"><?= count($solicitudes['data']) ?> en revisión</span>
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
                                <?php foreach ($oficinas['data'] as $oficina): ?>
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
                                <?php foreach ($tiposProducto['data'] ?? [] as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo'] ?>"> <!---Verificar si debe estar en STRING o INT-->
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
                        $totalSolicitudes = count($solicitudes['data'] ?? []);
                        $totalMonto = 0;
                        $totalTodosProductos = 0;
                        $totalTodosProdsSinProv = 0;
                        $urgentes = 0;

                        foreach ($solicitudes['data'] ?? [] as $solicitud) {
                            $hoy = new DateTime();
                            $fechaDeseo = new DateTime($solicitud['fecha_deseo']);
                            $diasDiferencia = $hoy->diff($fechaDeseo)->days;

                            if ($diasDiferencia <= 3)
                                $urgentes++;

                            foreach ($solicitud['productos'] ?? [] as $producto) {
                                $totalTodosProductos++;
                                if (empty($producto['rif_proveedor']))
                                    $totalTodosProdsSinProv++;
                            }
                        }
                        $proveedoresPorTipo = [];
                        foreach ($relacionesProvTipo['data'] ?? [] as $relacion) {
                            $id_tipo = $relacion['id_tipo'];
                            $rif_proveedor = $relacion['rif_proveedor'];

                            foreach ($proveedores ?? [] as $proveedor) {
                                if ($proveedor['rif'] == $rif_proveedor) {
                                    if (!isset($proveedoresPorTipo[$id_tipo])) {
                                        $proveedoresPorTipo[$id_tipo] = [];
                                    }
                                    $proveedoresPorTipo[$id_tipo][] = $proveedor;
                                    break;
                                }
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
                                <h3 id="countSinProveedor"><?= $totalTodosProdsSinProv ?></h3>
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
                    </div>

                    <!-- Lista de Solicitudes EN REVISIÓN -->
                    <div class="solicitudes-container" id="solicitudesList">
                        <?php if (!empty($solicitudes['data'])): ?>
                            <?php foreach ($solicitudes['data'] as $solicitud): ?>
                                <?php
                                $productos = $solicitud['productos'] ?? [];
                                $totalProductos = 0;
                                $totalUnidades = 0;
                                $asignadosUnProv = 0;
                                // Calcular prioridad basada en fecha deseada
                                $hoy = new DateTime();
                                $fechaDeseo = new DateTime($solicitud['fecha_deseo']);
                                $diasDiferencia = $hoy->diff($fechaDeseo)->days;

                                if ($diasDiferencia <= 3) {
                                    $prioridad = 'alta';
                                    $prioridadText = 'Alta';
                                    $prioridadClass = 'priority-high';
                                } elseif ($diasDiferencia <= 7) {
                                    $prioridad = 'media';
                                    $prioridadText = 'Media';
                                    $prioridadClass = 'priority-medium';
                                } else {
                                    $prioridad = 'baja';
                                    $prioridadText = 'Baja';
                                    $prioridadClass = 'priority-low';
                                }

                                foreach ($productos as $producto) {
                                    $totalProductos++;
                                    $totalUnidades += $producto['un_deseadas'];
                                }
                                ?>

                                <div class="solicitud-card" data-id="<?= $solicitud['id_solicitud'] ?>"
                                    data-prioridad="<?= $prioridad ?>" data-oficina="<?= $solicitud['num_oficina'] ?>"
                                    data-tipos="<?= implode(',', array_column($productos, 'id_tipo')) ?>"
                                    data-prov-asignados="<?= $asignadosUnProv ?>">

                                    <div class="solicitud-header">
                                        <div class="solicitud-info">
                                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                                <h3>Solicitud #<?= $solicitud['id_solicitud'] ?></h3>
                                                <span class="solicitud-priority <?= $prioridadClass ?>">
                                                    <i data-lucide="clock"></i> <?= $prioridadText ?>
                                                </span>
                                                <?php if ($totalProductos > 0): ?>
                                                    <span class="badge" style="background: var(--primary-light); color: white;">
                                                        <i data-lucide="alert-circle"></i> <?= $totalProductos ?> productos
                                                        distintos
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
                                                    <span>Oficina <?= htmlspecialchars($solicitud['nombre_oficina']) ?>
                                                        (<?= $solicitud['num_oficina'] ?>)</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i data-lucide="calendar"></i>
                                                    <span>Solicitado:
                                                        <?= date('d/m/Y', strtotime($solicitud['fecha_solic'])) ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i data-lucide="clock"></i>
                                                    <span>Entrega deseada:
                                                        <?= date('d/m/Y', strtotime($solicitud['fecha_deseo'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="solicitud-status status-revision">
                                            <i data-lucide="search"></i> En Revisión
                                        </div>
                                    </div>

                                    <?php if (!empty($solicitud['comentarios'])): ?>
                                        <div
                                            style="margin: 1rem 0; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-md);">
                                            <p style="margin: 0; color: var(--gray-700); font-style: italic;">
                                                <i data-lucide="message-square"
                                                    style="width: 16px; height: 16px; vertical-align: middle;"></i>
                                                <?= htmlspecialchars($solicitud['comentarios']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <!-- En el archivo HTML, modificar la sección de productos -->

                                    <!-- Resumen de asignación -->
                                    <div class="asignacion-summary">
                                        <div class="summary-header">
                                            <div
                                                style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                                <h4 style="font-weight: 600; color: var(--review-color); margin: 0;">
                                                    <i data-lucide="clipboard-check"></i> Resumen de asignación
                                                </h4>
                                                <div style="display: flex; align-items: center; gap: 1rem;">
                                                    <button class="btn-toggle-productos"
                                                        onclick="toggleProductos(<?= $solicitud['id_solicitud'] ?>)"
                                                        data-id="<?= $solicitud['id_solicitud'] ?>"
                                                        title="Mostrar/ocultar productos">
                                                        <i data-lucide="chevron-down"
                                                            id="chevron-<?= $solicitud['id_solicitud'] ?>"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="summary-content">
                                            <div class="summary-item">
                                                <h4>Productos concretos</h4>
                                                <p
                                                    style="color: var(--primary-color); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php
                                                    $nombresProductos = array_column($productos, 'nombre');
                                                    echo htmlspecialchars(implode(', ', $nombresProductos));
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="summary-item">
                                                <h4>Proveedores asignados</h4>
                                                <p style="color: <?= $asignadosUnProv > 0 ? 'var(--success-color)' : 'var(--warning-color)' ?>;"
                                                    id="contador-prov-<?= $solicitud['id_solicitud'] ?>">
                                                    <?= $asignadosUnProv ?> de <?= $totalProductos ?>
                                                </p>
                                            </div>
                                            <div class="summary-item">
                                                <h4>Días para entrega</h4>
                                                <p
                                                    style="color: <?= $diasDiferencia <= 3 ? 'var(--danger-color)' : ($diasDiferencia <= 7 ? 'var(--warning-color)' : 'var(--success-color)') ?>;">
                                                    <?= $diasDiferencia ?> días
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Productos solicitados (inicialmente ocultos) -->
                                    <div class="productos-container" id="productos-<?= $solicitud['id_solicitud'] ?>"
                                        style="display: none; margin-top: 1.5rem;">

                                        <!-- Contador de progreso -->
                                        <div class="progress-container" style="margin-bottom: 1rem;">
                                            <div
                                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                                <h5 style="margin: 0; color: var(--gray-700);">
                                                    <i data-lucide="package"></i> Productos detallados
                                                </h5>
                                            </div>
                                            <div class="progress-bar"
                                                style="height: 6px; background: var(--gray-200); border-radius: 3px; overflow: hidden;">
                                                <div id="barra-progreso-<?= $solicitud['id_solicitud'] ?>" style="height: 100%; width: <?= ($totalProductos > 0) ? ($asignadosUnProv / $totalProductos * 100) : 0 ?>%; 
                        background: linear-gradient(90deg, var(--success-color) 0%, #66BB6A 100%); 
                        transition: width 0.3s ease;"></div>
                                            </div>
                                        </div>

                                        <?php if (!empty($productos)): ?>
                                            <div class="productos-grid">
                                                <?php foreach ($productos as $producto): ?>
                                                    <?php
                                                    $tieneProveedor = !empty($producto['rif_proveedor']);
                                                    ?>
                                                    <div class="producto-item" data-tipo="<?= $producto['id_tipo'] ?>"
                                                        data-has-prov="<?= $tieneProveedor ? 'true' : 'false' ?>"
                                                        data-linea="<?= $producto['num_linea'] ?>"
                                                        data-id-producto="<?= $producto['id_producto'] ?>">

                                                        <div class="producto-header">
                                                            <div>
                                                                <span
                                                                    class="producto-name"><?= htmlspecialchars($producto['nombre']) ?></span>
                                                                <div class="producto-details">
                                                                    <span>Tipo:
                                                                        <?= htmlspecialchars($producto['nombre_tipo'] ?? 'N/A') ?></span>
                                                                </div>
                                                            </div>
                                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                                <span class="producto-quantity"><?= $producto['un_deseadas'] ?>
                                                                    <?= htmlspecialchars($producto['medida']) ?></span>

                                                                <!-- Botón para descartar producto -->
                                                                <button class="btn btn-small btn-danger btn-descartar-producto"
                                                                    onclick="descartarProducto(<?= $solicitud['id_solicitud'] ?>, <?= $producto['num_linea'] ?>, this)"
                                                                    title="Descartar este producto">
                                                                    <i data-lucide="trash-2"></i>
                                                                </button>

                                                                <?php if ($tieneProveedor): ?>
                                                                    <div class="proveedor-check">
                                                                        <i data-lucide="check" style="color: var(--success-color);"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Asignación de proveedor -->
                                                        <div class="proveedor-asignacion">
                                                            <div class="asignacion-form" data-linea="<?= $producto['num_linea'] ?>">
                                                                <div style="display: grid; gap: 1rem;">
                                                                    <!-- Primera línea: Cantidad -->
                                                                    <div>
                                                                        <label class="form-label"
                                                                            style="margin-bottom: 0.25rem; display: block;">Cantidad a
                                                                            suplir</label>
                                                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                            <input type="number"
                                                                                class="form-input cantidad-suplir-input"
                                                                                data-linea="<?= $producto['num_linea'] ?>"
                                                                                data-cantidad-inicial="<?= $producto['un_deseadas'] ?>"
                                                                                min="0" max="<?= $producto['un_deseadas'] * 2 ?>"
                                                                                value="<?= $producto['un_deseadas'] ?>"
                                                                                style="width: 120px;"
                                                                                onchange="validarCantidadSuplir(<?= $solicitud['id_solicitud'] ?>, <?= $producto['num_linea'] ?>, this)">
                                                                            <span
                                                                                style="white-space: nowrap; color: var(--gray-600); font-size: 0.875rem;">
                                                                                <?= htmlspecialchars($producto['medida']) ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Segunda línea: Proveedor -->
                                                                    <div>
                                                                        <label class="form-label"
                                                                            style="margin-bottom: 0.25rem; display: block;">Proveedor</label>
                                                                        <div style="display: flex; gap: 0.5rem;">
                                                                            <select class="form-select proveedor-select"
                                                                                data-tipo="<?= $producto['id_tipo'] ?>"
                                                                                data-linea="<?= $producto['num_linea'] ?>"
                                                                                onchange="cambiarProveedor(<?= $solicitud['id_solicitud'] ?>, <?= $producto['num_linea'] ?>, this.value)"
                                                                                style="flex: 1;">
                                                                                <option value="">Seleccionar proveedor</option>
                                                                                <!-- Las opciones se llenarán dinámicamente con JS -->
                                                                            </select>
                                                                        </div> <br>
                                                                        <button class="btn btn-small btn-secondary"
                                                                            onclick="buscarProveedor(<?= $producto['id_tipo'] ?>)"
                                                                            title="Buscar más proveedores" style="flex-shrink: 0;">
                                                                            <i data-lucide="search"></i>
                                                                            <span
                                                                                style="white-space: nowrap; color: var(--gray-600); font-size: 0.875rem;">
                                                                                Buscar proveedores
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="solicitud-actions">
                                        <div style="display: flex; gap: 1rem; align-items: center;">
                                            <span style="color: var(--gray-600); font-size: 0.875rem;">
                                                <i data-lucide="info"></i> Asigne proveedores a todos los productos antes de
                                                aprobar
                                            </span>
                                        </div>
                                        <div style="display: flex; gap: 1rem;">
                                            <button class="btn btn-danger"
                                                onclick="rechazarSolicitud(<?= $solicitud['id_solicitud'] ?>)">
                                                <i data-lucide="x-circle"></i>
                                                Rechazar
                                            </button>
                                            <button class="btn btn-success"
                                                onclick="aprobarSolicitud(<?= $solicitud['id_solicitud'] ?>)">
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
                                <p>Todas las solicitudes han sido gestionadas o no hay solicitudes pendientes de
                                    presupuesto.</p>
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
                <h2 style="margin-bottom: 1rem;"><i data-lucide="message-circle"></i> Solicitar Información
                    Adicional
                </h2>
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
                    <textarea class="form-input" id="mensajeInfo" rows="4"
                        placeholder="Describa qué información adicional necesita..."></textarea>
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
        let solicitudInfoActual = null;
        let proveedoresPorTipo = <?= json_encode($proveedoresPorTipo) ?>;
        let todosProveedores = <?= json_encode($proveedores) ?>;
        let cantidadesSuplir = {};

        // ========== FUNCIONES DE VALIDACIÓN DE CANTIDAD ==========

        function validarCantidadSuplir(idSolicitud, numLinea, inputElement) {
            const valor = parseInt(inputElement.value);
            const cantidadInicial = parseInt(inputElement.dataset.cantidadInicial);
            const minPermitido = Math.ceil(cantidadInicial * 0.6);

            // Validaciones básicas
            if (isNaN(valor) || valor < 0) {
                marcarCantidadInvalida(inputElement);
                return false;
            }

            if (valor < minPermitido) {
                marcarCantidadInvalida(inputElement);
                return false;
            }

            // Cantidad válida
            inputElement.classList.remove('cantidad-input-invalido', 'shake');

            // Guardar cantidad
            if (!cantidadesSuplir[idSolicitud]) {
                cantidadesSuplir[idSolicitud] = {};
            }
            cantidadesSuplir[idSolicitud][numLinea] = {
                valor: valor,
                valido: true,
                porcentaje: (valor / cantidadInicial) * 100
            };

            return true;
        }
        function validarTodasLasCantidades(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return true; // Si no existe, asumimos válido

            const inputsCantidad = solicitudDiv.querySelectorAll('.cantidad-suplir-input');
            let todasValidas = true;

            inputsCantidad.forEach(input => {
                const valor = parseInt(input.value) || 0;
                const cantidadInicial = parseInt(input.dataset.cantidadInicial);
                const minPermitido = 0;

                if (valor < minPermitido) {
                    marcarCantidadInvalida(input);
                    todasValidas = false;
                } else {
                    input.classList.remove('cantidad-input-invalido', 'shake');
                }
            });

            return todasValidas;
        }

        function marcarCantidadInvalida(inputElement) {
            inputElement.classList.add('cantidad-input-invalido', 'shake');

            // Remover animación después de 500ms
            setTimeout(() => {
                inputElement.classList.remove('shake');
            }, 500);

            // Enfocar el input
            inputElement.focus();
            inputElement.select();
        }

        // ========== FUNCIONES PARA DESPLEGAR/OCULTAR PRODUCTOS ==========

        function toggleProductos(idSolicitud) {
            const productosDiv = document.getElementById(`productos-${idSolicitud}`);
            const chevronIcon = document.getElementById(`chevron-${idSolicitud}`);
            const toggleBtn = document.querySelector(`.btn-toggle-productos[data-id="${idSolicitud}"]`);

            if (!productosDiv || !chevronIcon) return;

            if (productosDiv.style.display === 'none' || productosDiv.style.display === '') {
                // Mostrar productos
                productosDiv.style.display = 'block';
                productosDiv.classList.add('expanded');
                toggleBtn.classList.add('active');

                // Animación del chevron
                chevronIcon.setAttribute('data-lucide', 'chevron-up');

                // Si es la primera vez que se abre, calcular contadores
                if (!productosDiv.dataset.initialized) {
                    actualizarContadorProvAsignados(idSolicitud);
                    productosDiv.dataset.initialized = 'true';
                }

                // Crear iconos dentro del contenedor
                setTimeout(() => {
                    lucide.createIcons();
                }, 100);

            } else {
                // Ocultar productos
                productosDiv.style.display = 'none';
                productosDiv.classList.remove('expanded');
                toggleBtn.classList.remove('active');

                // Animación del chevron
                chevronIcon.setAttribute('data-lucide', 'chevron-down');
            }

            // Actualizar icono
            lucide.createIcons();
        }

        function expandirTodasSolicitudes() {
            const todasSolicitudes = document.querySelectorAll('.solicitud-card');
            todasSolicitudes.forEach(solicitudDiv => {
                const idSolicitud = solicitudDiv.dataset.id;
                const productosDiv = document.getElementById(`productos-${idSolicitud}`);

                if (productosDiv && (productosDiv.style.display === 'none' || productosDiv.style.display === '')) {
                    toggleProductos(idSolicitud);
                }
            });
        }

        function contraerTodasSolicitudes() {
            const todasSolicitudes = document.querySelectorAll('.solicitud-card');
            todasSolicitudes.forEach(solicitudDiv => {
                const idSolicitud = solicitudDiv.dataset.id;
                const productosDiv = document.getElementById(`productos-${idSolicitud}`);

                if (productosDiv && productosDiv.style.display === 'block') {
                    toggleProductos(idSolicitud);
                }
            });
        }

        // ========== MODIFICAR actualizarContadorProvAsignados ==========

        function actualizarContadorProvAsignados(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;

            const selects = solicitudDiv.querySelectorAll('.proveedor-select');
            let provAsignados = 0;

            // Contar solo selects con valor no vacío
            selects.forEach(select => {
                if (select.value && select.value.trim() !== "") {
                    provAsignados++;
                }
            });

            const totalProductos = selects.length;

            // 1. Actualizar el atributo data-prov-asignados
            solicitudDiv.dataset.provAsignados = provAsignados;

            // 2. Actualizar el contador en el resumen
            const contadorResumen = document.getElementById(`contador-prov-${idSolicitud}`);
            if (contadorResumen) {
                contadorResumen.textContent = `${provAsignados} de ${totalProductos}`;
                contadorResumen.style.color = provAsignados > 0 ? 'var(--success-color)' : 'var(--warning-color)';
            }

            // 3. Actualizar contador de progreso en productos desplegados
            const contadorProgreso = document.getElementById(`contador-progreso-${idSolicitud}`);
            const barraProgreso = document.getElementById(`barra-progreso-${idSolicitud}`);

            if (contadorProgreso) {
                contadorProgreso.textContent = provAsignados;
            }

            if (barraProgreso && totalProductos > 0) {
                const porcentaje = (provAsignados / totalProductos) * 100;
                barraProgreso.style.width = `${porcentaje}%`;

                // Cambiar color de la barra según progreso
                if (porcentaje === 100) {
                    barraProgreso.style.background = 'linear-gradient(90deg, var(--success-color) 0%, #66BB6A 100%)';
                } else if (porcentaje >= 50) {
                    barraProgreso.style.background = 'linear-gradient(90deg, var(--warning-color) 0%, #FFB74D 100%)';
                } else {
                    barraProgreso.style.background = 'linear-gradient(90deg, var(--danger-color) 0%, #EF5350 100%)';
                }
            }

            // 4. Actualizar badge de progreso en header
            actualizarBadgeProveedores(solicitudDiv, provAsignados, totalProductos);

            // 5. Actualizar visual de productos individuales
            actualizarVisualProductos(solicitudDiv);

            // 6. Verificar si se puede habilitar/deshabilitar el botón de aprobar
            verificarBotonAprobar(solicitudDiv, provAsignados, totalProductos);
        }

        function verificarBotonAprobar(solicitudDiv, provAsignados, totalProductos) {
            const botonAprobar = solicitudDiv.querySelector('.btn-success');
            if (!botonAprobar) return;

            // Verificar cantidades
            const idSolicitud = solicitudDiv.dataset.id;
            const cantidadesValidas = validarTodasLasCantidades(idSolicitud);
            const proveedoresCompletos = provAsignados === totalProductos;

            console.log(`Solicitud ${idSolicitud}: proveedores=${proveedoresCompletos}, cantidades=${cantidadesValidas}`);

            if (proveedoresCompletos && cantidadesValidas) {
                botonAprobar.disabled = false;
                botonAprobar.classList.remove('btn-disabled');
                botonAprobar.style.opacity = '1';
                botonAprobar.style.cursor = 'pointer';
                botonAprobar.title = 'Aprobar con presupuesto';
            } else {
                botonAprobar.disabled = true;
                botonAprobar.classList.add('btn-disabled');
                botonAprobar.style.opacity = '0.6';
                botonAprobar.style.cursor = 'not-allowed';

                // Tooltip informativo
                if (!proveedoresCompletos) {
                    botonAprobar.title = 'Faltan proveedores por asignar';
                } else if (!cantidadesValidas) {
                    botonAprobar.title = 'Algunas cantidades no cumplen el mínimo del 60%';
                } else {
                    botonAprobar.title = 'Completa todos los requisitos para aprobar';
                }
            }
        }
        function inicializarCantidadesYSincronizar(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;

            const inputsCantidad = solicitudDiv.querySelectorAll('.cantidad-suplir-input');

            inputsCantidad.forEach(input => {
                const valor = parseInt(input.value);
                const cantidadInicial = parseInt(input.dataset.cantidadInicial);

                // Si el valor es igual a la cantidad inicial, validarlo
                if (valor === cantidadInicial) {
                    const minPermitido = Math.ceil(cantidadInicial * 0.6);

                    if (valor >= minPermitido) {
                        // Es válido, remover clase de error si existe
                        input.classList.remove('cantidad-input-invalido', 'shake');

                        // Guardar en cantidadesSuplir
                        if (!cantidadesSuplir[idSolicitud]) {
                            cantidadesSuplir[idSolicitud] = {};
                        }
                        cantidadesSuplir[idSolicitud][input.dataset.linea] = {
                            valor: valor,
                            valido: true,
                            porcentaje: 100
                        };
                    } else {
                        // Marcar como inválido (aunque sea la cantidad inicial)
                        marcarCantidadInvalida(input);
                    }
                }
            });
        }

        function actualizarVisualProductos(solicitudDiv) {
            const productos = solicitudDiv.querySelectorAll('.producto-item');

            productos.forEach(producto => {
                const select = producto.querySelector('.proveedor-select');
                const hasProvider = select && select.value && select.value.trim() !== "";

                // Actualizar atributo data
                producto.dataset.hasProvider = hasProvider;

                // Actualizar check visual
                const checkContainer = producto.querySelector('.proveedor-check');

                if (hasProvider) {
                    // Producto CON proveedor
                    if (!checkContainer) {
                        // Crear contenedor de check si no existe
                        const headerDiv = producto.querySelector('.producto-header > div:last-child');
                        if (headerDiv) {
                            const newCheckContainer = document.createElement('div');
                            newCheckContainer.className = 'proveedor-check';
                            newCheckContainer.innerHTML = `<i data-lucide="check"></i>`;
                            headerDiv.appendChild(newCheckContainer);
                            lucide.createIcons();
                        }
                    } else {
                        // Actualizar icono existente
                        const icon = checkContainer.querySelector('i');
                        if (icon) {
                            icon.setAttribute('data-lucide', 'check');
                            icon.style.color = 'var(--success-color)';
                            lucide.createIcons();
                        }
                    }
                } else {
                    // Producto SIN proveedor - remover check visual
                    if (checkContainer) {
                        checkContainer.remove();
                    }
                }
            });
        }

        // ========== AGREGAR BOTONES DE EXPANDIR/CONTRARR TODOS ==========

        function agregarControlesExpandir() {
            const filtersSection = document.querySelector('.action-buttons');
            if (!filtersSection) return;

            // Crear contenedor para botones
            const expandControls = document.createElement('div');
            expandControls.style.display = 'flex';
            expandControls.style.gap = '0.5rem';
            expandControls.style.alignItems = 'center';

            // Botón para expandir todos
            const btnExpandAll = document.createElement('button');
            btnExpandAll.className = 'btn btn-small btn-secondary';
            btnExpandAll.innerHTML = '<i data-lucide="chevrons-down"></i>';
            btnExpandAll.onclick = expandirTodasSolicitudes;
            btnExpandAll.title = 'Mostrar todos los productos';

            // Botón para contraer todos
            const btnCollapseAll = document.createElement('button');
            btnCollapseAll.className = 'btn btn-small btn-secondary';
            btnCollapseAll.innerHTML = '<i data-lucide="chevrons-up"></i>';
            btnCollapseAll.onclick = contraerTodasSolicitudes;
            btnCollapseAll.title = 'Ocultar todos los productos';

            expandControls.appendChild(btnExpandAll);
            expandControls.appendChild(btnCollapseAll);

            // Agregar después de los botones existentes
            filtersSection.appendChild(expandControls);
            lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log("Documento cargado, inicializando...");

            // Inicializar filtros
            filtrarSolicitudes();

            // Inicializar filtros de proveedores
            inicializarFiltrosProveedores();

            // Agregar estilos
            agregarEstilosResaltado();

            // ========== AGREGAR ESTO ==========
            // Inicializar todas las cantidades y botones
            document.querySelectorAll('.solicitud-card').forEach(solicitudDiv => {
                const idSolicitud = solicitudDiv.dataset.id;

                // 1. Inicializar cantidades (valida que sean >= 60%)
                inicializarCantidadesYSincronizar(idSolicitud);

                // 2. Contar proveedores asignados inicialmente
                const selects = solicitudDiv.querySelectorAll('.proveedor-select');
                let provAsignados = 0;

                selects.forEach(select => {
                    if (select.value && select.value.trim() !== "") {
                        provAsignados++;
                    }
                });

                solicitudDiv.dataset.provAsignados = provAsignados;

                // 3. Verificar estado inicial del botón
                verificarBotonAprobar(solicitudDiv, provAsignados, selects.length);

                // 4. Event listeners para cambios en cantidades
                solicitudDiv.querySelectorAll('.cantidad-suplir-input').forEach(input => {
                    input.addEventListener('change', function () {
                        const numLinea = this.dataset.linea;

                        // Validar la cantidad
                        validarCantidadSuplir(idSolicitud, numLinea, this);

                        // Volver a verificar el botón
                        const currentProvAsignados = parseInt(solicitudDiv.dataset.provAsignados || 0);
                        verificarBotonAprobar(solicitudDiv, currentProvAsignados, selects.length);
                    });

                    input.addEventListener('input', function () {
                        // Validación en tiempo real
                        const valor = parseInt(this.value) || 0;
                        const cantidadInicial = parseInt(this.dataset.cantidadInicial);
                        const minPermitido = Math.ceil(cantidadInicial * 0.6);

                        if (valor < minPermitido) {
                            this.classList.add('cantidad-input-invalido');
                        } else {
                            this.classList.remove('cantidad-input-invalido');
                        }
                    });
                });

                // 5. Event listeners para cambios en proveedores
                selects.forEach(select => {
                    select.addEventListener('change', function () {
                        const numLinea = this.closest('[data-linea]').dataset.linea;
                        cambiarProveedor(idSolicitud, numLinea, this.value);
                    });
                });
            });
            // ========== FIN AGREGADO ==========

            // Cargar asignaciones existentes (si vienen del backend)
            document.querySelectorAll('.proveedor-select[selected]').forEach(select => {
                const solicitudId = select.closest('.solicitud-card').dataset.id;
                const numLinea = select.closest('[data-linea]').dataset.linea;
                const rifProveedor = select.value;

                if (solicitudId && numLinea && rifProveedor) {
                    cambiarProveedor(solicitudId, numLinea, rifProveedor);
                }
            });

            // Inicializar controles de expandir/contraer
            agregarControlesExpandir();
        });
        // ========== FUNCIONES DE FILTRADO ==========

        function filtrarProveedoresPorTipo(idTipo) {
            if (!proveedoresPorTipo || !proveedoresPorTipo[idTipo]) {
                // Si no hay proveedores específicos para este tipo, mostrar todos
                return todosProveedores || [];
            }
            return proveedoresPorTipo[idTipo];
        }
        function actualizarOpcionesProveedor(selectElement, idTipo) {
            if (!selectElement) return;

            // Guardar la selección actual
            const valorActual = selectElement.value;

            // Limpiar opciones excepto la primera
            while (selectElement.options.length > 1) {
                selectElement.remove(1);
            }

            // Obtener proveedores filtrados
            const proveedoresFiltrados = filtrarProveedoresPorTipo(idTipo);

            // Agregar nuevas opciones
            proveedoresFiltrados.forEach(proveedor => {
                const option = document.createElement('option');
                option.value = proveedor.rif;
                option.textContent = proveedor.nombre;
                option.dataset.info = JSON.stringify(proveedor);
                selectElement.appendChild(option);
            });

            // Restaurar selección anterior si existe en las nuevas opciones
            if (valorActual) {
                const existeOpcion = Array.from(selectElement.options).some(opt => opt.value === valorActual);
                if (existeOpcion) {
                    selectElement.value = valorActual;
                }
            }
        }
        function inicializarFiltrosProveedores() {
            document.querySelectorAll('.proveedor-select').forEach(select => {
                const tipo = select.dataset.tipo;
                if (tipo) {
                    actualizarOpcionesProveedor(select, parseInt(tipo));
                }
            });
        }
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
            actualizarContadorProvAsignados(idSolicitud);
            console.log(`Proveedor asignado: Solicitud ${idSolicitud}, Línea ${numLinea}, RIF ${rifProveedor}`);
        }

        function actualizarContadorProvAsignados(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;

            const selects = solicitudDiv.querySelectorAll('.proveedor-select');
            let provAsignados = 0;
            const totalProductos = selects.length;

            selects.forEach(select => {
                if (select.value != "") {
                    provAsignados++;
                }
            });
            solicitudDiv.dataset.provAsignados = provAsignados;

            // Actualizar badge
            const summaryItems = solicitudDiv.querySelectorAll('.summary-content .summary-item');
            if (summaryItems.length >= 2) {
                const proveedoresItem = summaryItems[1]; // Segundo item (índice 1)
                const contadorElement = proveedoresItem.querySelector('p');

                if (contadorElement) {
                    // Actualizar el texto y color
                    contadorElement.textContent = `${provAsignados} de ${totalProductos}`;
                    contadorElement.style.color = provAsignados > 0 ? 'var(--success-color)' : 'var(--warning-color)';
                    let badge = solicitudDiv.querySelector('.badge');
                    if (provAsignados > 0) {
                        if (badge) {
                            badge.remove();
                        }
                    }
                }
            }
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

            // Obtener productos NO descartados
            const productosActivos = solicitudDiv.querySelectorAll('.producto-item:not(.producto-descartado)');

            if (productosActivos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No hay productos activos',
                    text: 'Todos los productos han sido descartados. ¿Desea rechazar la solicitud?',
                    showCancelButton: true,
                    confirmButtonText: 'Rechazar solicitud',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#F44336'
                }).then((result) => {
                    if (result.isConfirmed) {
                        rechazarSolicitud(idSolicitud);
                    }
                });
                return;
            }

            // Validar proveedores en productos activos
            let faltanProveedores = false;
            productosActivos.forEach(producto => {
                const select = producto.querySelector('.proveedor-select');
                if (select && !select.value) {
                    faltanProveedores = true;
                }
            });

            if (faltanProveedores) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Proveedores faltantes',
                    text: 'Debe asignar un proveedor a todos los productos activos antes de aprobar.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Validar cantidades en productos activos
            let problemasCantidades = [];
            productosActivos.forEach(producto => {
                const inputCantidad = producto.querySelector('.cantidad-suplir-input');

                if (inputCantidad) {
                    const cantidadInicial = parseInt(inputCantidad.dataset.cantidadInicial);
                    const cantidadSuplir = parseInt(inputCantidad.value) || 0;
                    const minPermitido = Math.ceil(cantidadInicial * 0.6);

                    if (cantidadSuplir < minPermitido) {
                        const nombre = producto.querySelector('.producto-name').textContent;
                        problemasCantidades.push({
                            nombre: nombre,
                            solicitado: cantidadInicial,
                            suplir: cantidadSuplir,
                            minimo: minPermitido
                        });
                    }
                }
            });

            if (problemasCantidades.length > 0) {
                let mensaje = 'Las siguientes cantidades no cumplen con el mínimo del 60%:<br><br>';
                problemasCantidades.forEach(problema => {
                    mensaje += `• <strong>${problema.nombre}:</strong> ${problema.suplir} de ${problema.solicitado} `;
                    mensaje += `(mínimo: ${problema.minimo})<br>`;
                });

                Swal.fire({
                    icon: 'warning',
                    title: 'Cantidades insuficientes',
                    html: mensaje,
                    confirmButtonText: 'Corregir',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar'
                });
                return;
            }

            // Recolectar datos para inserción en registro_prod
            const asignaciones = [];

            productosActivos.forEach(producto => {
                const select = producto.querySelector('.proveedor-select');
                const inputCantidad = producto.querySelector('.cantidad-suplir-input');
                const linea = producto.querySelector('[data-linea]').dataset.linea;
                const id = producto.dataset.idProducto; 
                console.log('id: ',id);
                const tipo = producto.dataset.tipo;

                if (select && select.value && inputCantidad) {
                    const cantidadSolicitada = parseInt(inputCantidad.dataset.cantidadInicial) || 0;
                    const cantidadSuplir = parseInt(inputCantidad.value) || 0;

                    // Calcular cantidad adicional (un_anadidas)
                    const cantidadAdicional = cantidadSuplir - cantidadSolicitada;

                    asignaciones.push({
                        id_solicitud: idSolicitud,
                        num_linea: parseInt(linea),
                        rif_proveedor: select.value,
                        id_tipo: tipo,
                        id_producto: id,
                        cantidad_solicitada: cantidadSolicitada,
                        cantidad_suplir: cantidadSuplir,
                        un_anadidas: cantidadAdicional
                    });
                }
            });

            // Mostrar resumen final
            const resumen = generarResumenFinal(asignaciones);

            Swal.fire({
                title: '¿Confirmar aprobación?',
                html: `
            <div style="text-align: left;">
                <p><strong>Resumen de la aprobación:</strong></p>
                <p>• Productos aprobados: ${asignaciones.length}</p>
                <p>• Total solicitado: ${resumen.totalSolicitado} unidades</p>
                <p>• Total a suplir: ${resumen.totalSuplir} unidades</p>
                <p>• Adicional aprobado: ${resumen.totalAdicional} unidades</p>
                <p>• Porcentaje: ${resumen.porcentajeTotal}%</p>
                ${productosDescartados[idSolicitud] && productosDescartados[idSolicitud].length > 0 ?
                        `<p><strong>Nota:</strong> ${productosDescartados[idSolicitud].length} productos descartados</p>` : ''}
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Aprobar definitivamente',
                cancelButtonText: 'Revisar nuevamente',
                confirmButtonColor: '#4CAF50',
                showDenyButton: false,
                denyButtonText: 'Generar comprobante',
                denyButtonColor: '#2196F3'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await procesarAprobacion(idSolicitud, asignaciones, solicitudDiv);
                } else if (result.dismiss === Swal.DismissReason.deny) {
                    // Generar comprobante PDF
                    await generarComprobante(idSolicitud, asignaciones);
                }
            });
        }

        function generarResumenFinal(asignaciones) {
            let totalSolicitado = 0;
            let totalSuplir = 0;
            let totalAdicional = 0;

            asignaciones.forEach(asignacion => {
                totalSolicitado += asignacion.cantidad_solicitada;
                totalSuplir += asignacion.cantidad_suplir;
                totalAdicional += asignacion.un_anadidas;
            });

            const porcentajeTotal = totalSolicitado > 0 ?
                ((totalSuplir / totalSolicitado) * 100).toFixed(1) : 0;

            return {
                totalSolicitado: totalSolicitado,
                totalSuplir: totalSuplir,
                totalAdicional: totalAdicional,
                porcentajeTotal: porcentajeTotal + '%'
            };
        }

        async function procesarAprobacion(idSolicitud, asignaciones, solicitudDiv) {
            try {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando aprobación...',
                    text: 'Actualizando registros en el sistema',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar datos al backend
                const response = await fetch('?action=solicitudes&method=aprobarSolicitud', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'aprobarSolicitud',
                        id_solicitud: idSolicitud,
                        asignaciones: asignaciones,
                        productos_descartados: productosDescartados[idSolicitud] || []
                    })
                });

                const result = await response.json();

                Swal.close();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Aprobación exitosa!',
                        html: `
                    <div style="text-align: left;">
                        <p>Solicitud #${idSolicitud} aprobada correctamente.</p>
                        <p>Se han creado ${asignaciones.length} registros en el sistema.</p>
                        <p><strong>Estado:</strong> ${result.estado || 'Aprobada'}</p>
                    </div>
                `,
                        timer: 3000,
                        showConfirmButton: false
                    });

                    // Eliminar solicitud de la vista después de 3 segundos
                    setTimeout(() => {
                        solicitudDiv.remove();

                        // Verificar si quedan más solicitudes
                        const totalSolicitudes = document.querySelectorAll('.solicitud-card').length;
                        if (totalSolicitudes === 0) {
                            mostrarMensajeNoResultados(true);
                        }

                        // Limpiar de variables globales
                        delete asignacionesProveedor[idSolicitud];
                        delete productosDescartados[idSolicitud];
                        delete cantidadesSuplir[idSolicitud];

                    }, 3000);

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la aprobación',
                        text: result.message || 'No se pudo completar la aprobación.',
                        confirmButtonText: 'Entendido'
                    });
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.',
                    confirmButtonText: 'Entendido'
                });
            }
        }
        async function generarComprobante(idSolicitud, asignaciones) {
            try {
                Swal.fire({
                    title: 'Generando comprobante...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch('controller/ReporteController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'comprobante_aprobacion',
                        id_solicitud: idSolicitud,
                        asignaciones: asignaciones
                    })
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `Comprobante_Aprobacion_${idSolicitud}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();

                    Swal.close();

                    // Preguntar si aprobar después de generar comprobante
                    Swal.fire({
                        title: '¿Aprobar solicitud?',
                        text: 'El comprobante se ha descargado. ¿Desea aprobar la solicitud ahora?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, aprobar',
                        cancelButtonText: 'No, solo comprobante',
                        confirmButtonColor: '#4CAF50'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
                            procesarAprobacion(idSolicitud, asignaciones, solicitudDiv);
                        }
                    });
                } else {
                    throw new Error('Error generando comprobante');
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo generar el comprobante.'
                });
            }
        }
        function generarResumenSimple(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return { totalSuplir: 0, totalSolicitado: 0, porcentajeTotal: '0%' };

            const inputsCantidad = solicitudDiv.querySelectorAll('.cantidad-suplir-input');
            let totalSolicitado = 0;
            let totalSuplir = 0;

            inputsCantidad.forEach(input => {
                totalSolicitado += parseInt(input.dataset.cantidadInicial) || 0;
                totalSuplir += parseInt(input.value) || 0;
            });

            const porcentajeTotal = totalSolicitado > 0 ?
                ((totalSuplir / totalSolicitado) * 100).toFixed(1) : 0;

            return {
                totalSolicitado: totalSolicitado,
                totalSuplir: totalSuplir,
                porcentajeTotal: porcentajeTotal + '%'
            };
        }

        function crearBotonLimpiarResaltadosCantidades(idSolicitud, productosInvalidos) {
            const productosDiv = document.getElementById(`productos-${idSolicitud}`);
            if (!productosDiv) return;

            let botonLimpiar = productosDiv.querySelector('.btn-limpiar-resaltados-cantidad');

            if (!botonLimpiar) {
                botonLimpiar = document.createElement('button');
                botonLimpiar.className = 'btn btn-small btn-outline-danger btn-limpiar-resaltados-cantidad';
                botonLimpiar.innerHTML = '<i data-lucide="x-circle"></i> Quitar resaltados de cantidades';
                botonLimpiar.style.cssText = `
            margin-top: 1rem;
            margin-left: auto;
            display: block;
            border-color: var(--danger-color);
            color: var(--danger-color);
        `;
                botonLimpiar.onclick = function () {
                    productosInvalidos.forEach(producto => {
                        const input = producto.querySelector('.cantidad-suplir-input');
                        if (input) {
                            input.classList.remove('cantidad-input-invalido');
                            input.style.animation = '';
                        }
                    });
                    this.remove();
                };

                const progressContainer = productosDiv.querySelector('.progress-container');
                if (progressContainer) {
                    progressContainer.appendChild(botonLimpiar);
                } else {
                    productosDiv.insertBefore(botonLimpiar, productosDiv.firstChild);
                }

                lucide.createIcons();
            }
        }

        // Función para crear botón de limpiar resaltados
        function crearBotonLimpiarResaltados(idSolicitud, productosConFalta) {
            const productosDiv = document.getElementById(`productos-${idSolicitud}`);
            if (!productosDiv) return;

            // Verificar si ya existe el botón
            let botonLimpiar = productosDiv.querySelector('.btn-limpiar-resaltados');

            if (!botonLimpiar) {
                // Crear botón
                botonLimpiar = document.createElement('button');
                botonLimpiar.className = 'btn btn-small btn-outline-warning btn-limpiar-resaltados';
                botonLimpiar.innerHTML = '<i data-lucide="x-circle"></i> Quitar resaltados';
                botonLimpiar.style.cssText = `
                    margin-top: 1rem;
                    margin-left: auto;
                    display: block;
                    border-color: var(--warning-color);
                    color: var(--warning-color);
                `;
                botonLimpiar.onclick = function () {
                    limpiarResaltados(idSolicitud, productosConFalta);
                    this.remove();
                };

                // Agregar al final del contenedor de productos
                const progressContainer = productosDiv.querySelector('.progress-container');
                if (progressContainer) {
                    progressContainer.appendChild(botonLimpiar);
                } else {
                    productosDiv.insertBefore(botonLimpiar, productosDiv.firstChild);
                }

                lucide.createIcons();
            }
        }

        // Función para limpiar resaltados
        function limpiarResaltados(idSolicitud, productosConFalta) {
            productosConFalta.forEach(producto => {
                producto.classList.remove('producto-faltante');
                producto.style.animation = '';

                const select = producto.querySelector('.proveedor-select');
                if (select) {
                    select.classList.remove('select-faltante');
                    select.style.borderColor = '';
                    select.style.boxShadow = '';
                }
            });
        }

        // Agregar CSS para los estilos de resaltado
        function agregarEstilosResaltado() {
            const estilosExistentes = document.querySelector('#estilos-resaltado');
            if (estilosExistentes) return;

            const style = document.createElement('style');
            style.id = 'estilos-resaltado';
            style.textContent = `
                @keyframes pulse-warning {
                    0% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.7); }
                    70% { box-shadow: 0 0 0 10px rgba(255, 152, 0, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0); }
                }
                
                .producto-faltante {
                    border: 2px solid var(--warning-color) !important;
                    background: linear-gradient(90deg, rgba(255, 152, 0, 0.1) 0%, rgba(255, 152, 0, 0.05) 100%) !important;
                    position: relative;
                }
                
                .producto-faltante::before {
                    content: "¡Falta asignar proveedor!";
                    position: absolute;
                    top: -10px;
                    right: 10px;
                    background: var(--warning-color);
                    color: white;
                    padding: 2px 8px;
                    border-radius: 4px;
                    font-size: 0.7rem;
                    font-weight: 600;
                    z-index: 10;
                }
                
                .select-faltante {
                    border-color: var(--warning-color) !important;
                    box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.3) !important;
                }
                
                .btn-outline-warning {
                    border: 2px solid var(--warning-color);
                    color: var(--warning-color);
                    background: transparent;
                    padding: 0.5rem 1rem;
                    border-radius: var(--radius-md);
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                
                .btn-outline-warning:hover {
                    background: var(--warning-color);
                    color: white;
                }
            `;
            document.head.appendChild(style);
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
                    const response = await fetch('?action=solicitudes&method=rechazarSolicitud', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
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
                const response = await fetch('?action=solicitudes&method=solicitarInfo', {
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
                const proveedoresFiltrados = filtrarProveedoresPorTipo(idTipo);

                if (!proveedoresFiltrados || proveedoresFiltrados.length === 0) {
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
                    <i data-lucide="building"></i> Proveedores disponibles (${proveedoresFiltrados.length})
                </h2>
                <p style="color: var(--gray-600);">Seleccione un proveedor para asignar:</p>
                <div class="input-group" style="margin-top: 1rem;">
                    <input type="text" id="searchProveedorInput" 
                           class="form-input" 
                           placeholder="Buscar proveedor..." 
                           style="margin-right: 0.5rem;"
                           onkeyup="filtrarProveedoresModal(${idTipo})">
                </div>
            </div>
            
            <div id="resultadosProveedor" style="display: grid; gap: 1rem; max-height: 400px; overflow-y: auto;">
                <!-- Los resultados se cargarán aquí -->
            </div>
        `;

                // Mostrar todos los proveedores inicialmente
                mostrarProveedoresEnModal(proveedoresFiltrados, idTipo);

                document.getElementById('proveedorModal').classList.add('active');

                // Enfocar el input de búsqueda
                setTimeout(() => {
                    const searchInput = document.getElementById('searchProveedorInput');
                    if (searchInput) searchInput.focus();
                }, 100);

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los proveedores.'
                });
            }
        }

        function mostrarProveedoresEnModal(proveedores, idTipo) {
            const resultadosDiv = document.getElementById('resultadosProveedor');

            if (!proveedores || proveedores.length === 0) {
                resultadosDiv.innerHTML = `
            <div class="empty-state" style="padding: 2rem;">
                <i data-lucide="users" style="font-size: 3rem; color: var(--gray-300);"></i>
                <h3 style="margin-top: 1rem;">No se encontraron proveedores</h3>
            </div>
        `;
                return;
            }

            let html = `<div style="display: grid; gap: 1rem;">`;

            proveedores.forEach(proveedor => {
                const infoProveedor = JSON.stringify(proveedor).replace(/"/g, '&quot;');
                html += `
            <div class="proveedor-item" data-nombre="${proveedor.nombre.toLowerCase()}" data-rif="${proveedor.rif}">
                <div style="padding: 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem;">${proveedor.nombre}</h4>
                            <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">
                                RIF: ${proveedor.rif} | Tel: ${proveedor.telefono}
                            </p>
                            <p style="margin: 0.5rem 0 0 0; color: var(--gray-600); font-size: 0.875rem;">
                                Email: ${proveedor.email}
                            </p>
                            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem; font-size: 0.75rem;">
                                <span style="background: ${proveedor.estado === 'Activo' ? '#E8F5E9' : '#FFEBEE'}; 
                                      color: ${proveedor.estado === 'Activo' ? '#4CAF50' : '#F44336'}; 
                                      padding: 0.25rem 0.5rem; border-radius: 12px;">
                                    ${proveedor.estado}
                                </span>
                                <span style="color: var(--gray-500);">${proveedor.nota || 'Sin notas'}</span>
                            </div>
                        </div>
                        <button class="btn btn-small btn-primary" 
                                onclick="seleccionarProveedorModal('${proveedor.rif}', '${proveedor.nombre.replace(/'/g, "\\'")}', ${idTipo})"
                                style="white-space: nowrap;">
                            Seleccionar
                        </button>
                    </div>
                </div>
            </div>
        `;
            });

            html += `</div>`;
            resultadosDiv.innerHTML = html;
        }

        function filtrarProveedoresModal(idTipo) {
            const searchInput = document.getElementById('searchProveedorInput');
            const termino = searchInput ? searchInput.value.toLowerCase() : '';

            const proveedoresFiltrados = filtrarProveedoresPorTipo(idTipo);

            if (!termino) {
                // Mostrar todos si no hay término de búsqueda
                mostrarProveedoresEnModal(proveedoresFiltrados, idTipo);
                return;
            }

            // Filtrar por término de búsqueda
            const resultados = proveedoresFiltrados.filter(proveedor =>
                proveedor.nombre.toLowerCase().includes(termino) ||
                proveedor.rif.toLowerCase().includes(termino) ||
                (proveedor.email && proveedor.email.toLowerCase().includes(termino)) ||
                (proveedor.nota && proveedor.nota.toLowerCase().includes(termino))
            );

            mostrarProveedoresEnModal(resultados, idTipo);
        }

        function seleccionarProveedorModal(rif, nombre, idTipo) {
            // Encontrar todos los selects visibles del tipo correspondiente
            const selects = document.querySelectorAll(`.proveedor-select[data-tipo="${idTipo}"]:not([value])`);

            if (selects.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Todos asignados',
                    text: 'Todos los productos de este tipo ya tienen proveedor asignado.'
                });
                return;
            }

            // Expandir todas las solicitudes que contengan productos de este tipo
            selects.forEach(select => {
                const solicitudDiv = select.closest('.solicitud-card');
                if (solicitudDiv) {
                    const idSolicitud = solicitudDiv.dataset.id;
                    const productosDiv = document.getElementById(`productos-${idSolicitud}`);

                    // Expandir si está oculto
                    if (productosDiv && (productosDiv.style.display === 'none' || productosDiv.style.display === '')) {
                        toggleProductos(idSolicitud);
                    }
                }
            });

            // Asignar proveedor después de un breve delay para que se vea la animación
            setTimeout(() => {
                selects.forEach(select => {
                    select.value = rif;

                    // Actualizar la solicitud correspondiente
                    const solicitudDiv = select.closest('.solicitud-card');
                    if (solicitudDiv) {
                        const idSolicitud = solicitudDiv.dataset.id;
                        const numLinea = select.closest('[data-linea]').dataset.linea;

                        // Actualizar asignación
                        if (!asignacionesProveedor[idSolicitud]) {
                            asignacionesProveedor[idSolicitud] = {};
                        }
                        asignacionesProveedor[idSolicitud][numLinea] = rif;

                        // Actualizar visualmente
                        actualizarContadorProvAsignados(idSolicitud);
                    }
                });

                document.getElementById('proveedorModal').classList.remove('active');

                Swal.fire({
                    icon: 'success',
                    title: 'Proveedor asignado',
                    text: `Proveedor "${nombre}" asignado a ${selects.length} productos.`,
                    timer: 2000
                });
            }, 300);
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

        // Llamar a la función para agregar estilos cuando se cargue la página
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar filtros
            filtrarSolicitudes();

            // Inicializar filtros de proveedores
            inicializarFiltrosProveedores();

            // Configurar contadores iniciales
            document.querySelectorAll('.solicitud-card').forEach(solicitudDiv => {
                const idSolicitud = solicitudDiv.dataset.id;
                actualizarContadorProvAsignados(idSolicitud);
            });

            // Configurar event listeners para selects
            document.querySelectorAll('.proveedor-select').forEach(select => {
                select.addEventListener('change', function () {
                    const solicitudDiv = this.closest('.solicitud-card');
                    if (solicitudDiv) {
                        const idSolicitud = solicitudDiv.dataset.id;
                        const numLinea = this.dataset.linea;
                        cambiarProveedor(idSolicitud, numLinea, this.value);
                    }
                });
            });

            // Crear iconos
            lucide.createIcons();

            // Configurar contadores iniciales
            document.querySelectorAll('.solicitud-card').forEach(solicitudDiv => {
                const idSolicitud = solicitudDiv.dataset.id;
                actualizarContadorProvAsignados(idSolicitud);
            });

            // Configurar event listeners para selects
            document.querySelectorAll('.proveedor-select').forEach(select => {
                select.addEventListener('change', function () {
                    const solicitudDiv = this.closest('.solicitud-card');
                    if (solicitudDiv) {
                        const idSolicitud = solicitudDiv.dataset.id;
                        const numLinea = this.closest('[data-linea]').dataset.linea;
                        cambiarProveedor(idSolicitud, numLinea, this.value);
                    }
                });

                // Inicializar asignaciones si hay valor
                if (select.value && select.value !== "") {
                    const solicitudDiv = select.closest('.solicitud-card');
                    if (solicitudDiv) {
                        const idSolicitud = solicitudDiv.dataset.id;
                        const numLinea = select.closest('[data-linea]').dataset.linea;

                        if (!asignacionesProveedor[idSolicitud]) {
                            asignacionesProveedor[idSolicitud] = {};
                        }
                        asignacionesProveedor[idSolicitud][numLinea] = select.value;
                    }
                }
            });

            // Crear iconos iniciales
            lucide.createIcons();

            // Opcional: Expandir automáticamente si hay pocas solicitudes
            const totalSolicitudes = document.querySelectorAll('.solicitud-card').length;
            if (totalSolicitudes <= 3) {
                expandirTodasSolicitudes();
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            agregarEstilosResaltado();
        });
        // Agregar animación CSS para el resaltado
        const style = document.createElement('style');
        style.textContent = `
                @keyframes pulse-warning {
                    0% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.7); }
                    70% { box-shadow: 0 0 0 10px rgba(255, 152, 0, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0); }
                }
                
                .producto-faltante {
                    animation: pulse-warning 2s infinite !important;
                    border: 2px solid var(--warning-color) !important;
                    background: linear-gradient(90deg, rgba(255, 152, 0, 0.1) 0%, rgba(255, 152, 0, 0.05) 100%) !important;
                }
                
                .select-faltante {
                    border-color: var(--warning-color) !important;
                    box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.3) !important;
                }
            `;
        document.head.appendChild(style);
        document.addEventListener('DOMContentLoaded', function () {
            console.log("Documento cargado, inicializando...");

            // Inicializar filtros
            filtrarSolicitudes();

            // Inicializar filtros de proveedores
            inicializarFiltrosProveedores();

            // Agregar estilos
            agregarEstilosResaltado();

            // ========== INICIALIZACIÓN DE CANTIDADES Y BOTONES ==========
            inicializarTodasLasSolicitudes();


            // Cargar asignaciones existentes del backend
            cargarAsignacionesExistentes();
        });
        // Variable global para productos descartados
        let productosDescartados = {};

        function descartarProducto(idSolicitud, numLinea, botonElement) {
            const productoItem = botonElement.closest('.producto-item');
            const nombreProducto = productoItem.querySelector('.producto-name').textContent;

            Swal.fire({
                title: '¿Descartar producto?',
                html: `¿Está seguro de descartar el producto <strong>"${nombreProducto}"</strong>?<br>
               Este producto ya no será obligatorio para aprobar la solicitud.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#F44336'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Marcar como descartado
                    productoItem.classList.add('producto-descartado');

                    // Deshabilitar todos los inputs y selects
                    productoItem.querySelectorAll('input, select, button').forEach(element => {
                        if (!element.classList.contains('producto-restaurar-btn')) {
                            element.disabled = true;
                            element.style.pointerEvents = 'none';
                        }
                    });

                    // Cambiar botón de descartar por botón de restaurar
                    const botonContenedor = botonElement.parentElement;
                    botonElement.style.display = 'none';

                    const restaurarBtn = document.createElement('button');
                    restaurarBtn.className = 'producto-restaurar-btn';
                    restaurarBtn.innerHTML = '<i data-lucide="refresh-ccw"></i> Restaurar';
                    restaurarBtn.onclick = function () {
                        restaurarProducto(idSolicitud, numLinea, this);
                    };
                    botonContenedor.appendChild(restaurarBtn);

                    // Agregar badge de descartado
                    const badge = document.createElement('span');
                    badge.className = 'producto-descartado-badge';
                    badge.innerHTML = '<i data-lucide="x-circle"></i> Descartado';
                    productoItem.querySelector('.producto-header').appendChild(badge);

                    // Guardar en variable global
                    if (!productosDescartados[idSolicitud]) {
                        productosDescartados[idSolicitud] = [];
                    }
                    productosDescartados[idSolicitud].push(numLinea);

                    // Actualizar iconos
                    lucide.createIcons();

                    // Actualizar estado del botón de aprobar
                    actualizarEstadoSolicitud(idSolicitud);

                    Swal.fire({
                        icon: 'success',
                        title: 'Producto descartado',
                        text: 'El producto ha sido descartado y ya no es obligatorio.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function restaurarProducto(idSolicitud, numLinea, botonElement) {
            const productoItem = botonElement.closest('.producto-item');
            const nombreProducto = productoItem.querySelector('.producto-name').textContent;

            // Quitar clase de descartado
            productoItem.classList.remove('producto-descartado');

            // Habilitar todos los inputs y selects
            productoItem.querySelectorAll('input, select, button').forEach(element => {
                element.disabled = false;
                element.style.pointerEvents = '';
            });

            // Remover badge de descartado
            const badge = productoItem.querySelector('.producto-descartado-badge');
            if (badge) badge.remove();

            // Remover botón de restaurar
            botonElement.remove();

            // Mostrar botón de descartar nuevamente
            const btnDescartar = productoItem.querySelector('.btn-descartar-producto');
            if (btnDescartar) {
                btnDescartar.style.display = '';
            }

            // Remover de productos descartados
            if (productosDescartados[idSolicitud]) {
                const index = productosDescartados[idSolicitud].indexOf(numLinea);
                if (index > -1) {
                    productosDescartados[idSolicitud].splice(index, 1);
                }
            }

            // Actualizar iconos
            lucide.createIcons();

            // Actualizar estado del botón de aprobar
            actualizarEstadoSolicitud(idSolicitud);

            Swal.fire({
                icon: 'success',
                title: 'Producto restaurado',
                text: 'El producto ha sido restaurado y ahora es obligatorio nuevamente.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // ========== NUEVA FUNCIÓN PARA INICIALIZAR TODO ==========

        function inicializarTodasLasSolicitudes() {
            console.log("Inicializando todas las solicitudes...");

            document.querySelectorAll('.solicitud-card').forEach(solicitudDiv => {
                const idSolicitud = solicitudDiv.dataset.id;
                console.log(`Procesando solicitud ${idSolicitud}`);

                // 1. Contar proveedores asignados inicialmente
                const selects = solicitudDiv.querySelectorAll('.proveedor-select');
                let provAsignados = 0;

                selects.forEach(select => {
                    // Verificar si el select tiene un valor seleccionado
                    if (select.value && select.value.trim() !== "") {
                        provAsignados++;
                        console.log(`  Select con proveedor: ${select.value}`);
                    } else {
                        console.log(`  Select sin proveedor`);
                    }
                });

                solicitudDiv.dataset.provAsignados = provAsignados;
                console.log(`  Proveedores asignados: ${provAsignados}/${selects.length}`);

                // 2. Inicializar y validar TODAS las cantidades
                const inputsCantidad = solicitudDiv.querySelectorAll('.cantidad-suplir-input');
                let todasCantidadesValidas = true;

                inputsCantidad.forEach(input => {
                    const valor = parseInt(input.value) || 0;
                    const cantidadInicial = parseInt(input.dataset.cantidadInicial) || 0;
                    const minPermitido = Math.ceil(cantidadInicial * 0.6);

                    console.log(`  Cantidad: ${valor}/${cantidadInicial}, Mínimo: ${minPermitido}`);

                    // Validar si cumple con el mínimo
                    if (valor < minPermitido) {
                        console.log(`  ❌ Cantidad INVALIDA para línea ${input.dataset.linea}`);
                        input.classList.add('cantidad-input-invalido');
                        todasCantidadesValidas = false;
                    } else {
                        console.log(`  ✅ Cantidad válida para línea ${input.dataset.linea}`);
                        input.classList.remove('cantidad-input-invalido');
                    }

                    // Guardar en cantidadesSuplir
                    if (!cantidadesSuplir[idSolicitud]) {
                        cantidadesSuplir[idSolicitud] = {};
                    }
                    cantidadesSuplir[idSolicitud][input.dataset.linea] = {
                        valor: valor,
                        valido: (valor >= minPermitido),
                        porcentaje: cantidadInicial > 0 ? (valor / cantidadInicial * 100) : 0
                    };
                });

                // 3. Verificar estado inicial del botón
                verificarBotonAprobar(solicitudDiv, provAsignados, selects.length, todasCantidadesValidas);

                // 4. Configurar event listeners
                configurarEventListenersSolicitud(solicitudDiv);
            });
        }

        // ========== FUNCIÓN MEJORADA PARA VERIFICAR BOTÓN ==========

        function verificarBotonAprobar(solicitudDiv, provAsignados, totalProductos, todasCantidadesValidas = null) {
            const botonAprobar = solicitudDiv.querySelector('.btn-success');
            if (!botonAprobar) return;

            const idSolicitud = solicitudDiv.dataset.id;

            // Contar productos NO descartados
            const productosNoDescartados = solicitudDiv.querySelectorAll('.producto-item:not(.producto-descartado)').length;

            // Contar proveedores asignados solo en productos no descartados
            let provAsignadosNoDescartados = 0;
            solicitudDiv.querySelectorAll('.producto-item:not(.producto-descartado) .proveedor-select').forEach(select => {
                if (select.value && select.value.trim() !== "") {
                    provAsignadosNoDescartados++;
                }
            });

            // Verificar cantidades solo en productos no descartados
            if (todasCantidadesValidas === null) {
                todasCantidadesValidas = true;
                solicitudDiv.querySelectorAll('.producto-item:not(.producto-descartado) .cantidad-suplir-input').forEach(input => {
                    const valor = parseInt(input.value) || 0;
                    const cantidadInicial = parseInt(input.dataset.cantidadInicial);
                    const minPermitido = Math.ceil(cantidadInicial * 0.6);

                    if (valor < minPermitido) {
                        todasCantidadesValidas = false;
                    }
                });
            }

            const proveedoresCompletos = provAsignadosNoDescartados === productosNoDescartados && productosNoDescartados > 0;

            console.log(`Verificando botón: 
        Productos totales: ${totalProductos}
        Productos descartados: ${productosDescartados[idSolicitud] ? productosDescartados[idSolicitud].length : 0}
        Productos activos: ${productosNoDescartados}
        Proveedores asignados: ${provAsignadosNoDescartados}/${productosNoDescartados}
        Cantidades válidas: ${todasCantidadesValidas}`);

            if (proveedoresCompletos && todasCantidadesValidas && productosNoDescartados > 0) {
                botonAprobar.disabled = false;
                botonAprobar.classList.remove('btn-disabled');
                botonAprobar.style.opacity = '1';
                botonAprobar.style.cursor = 'pointer';
                botonAprobar.title = 'Aprobar con presupuesto';
            } else {
                botonAprobar.disabled = true;
                botonAprobar.classList.add('btn-disabled');
                botonAprobar.style.opacity = '0.6';
                botonAprobar.style.cursor = 'not-allowed';

                if (productosNoDescartados === 0) {
                    botonAprobar.title = 'Todos los productos han sido descartados';
                } else if (!proveedoresCompletos) {
                    botonAprobar.title = `Faltan ${productosNoDescartados - provAsignadosNoDescartados} proveedores por asignar`;
                } else if (!todasCantidadesValidas) {
                    botonAprobar.title = 'Algunas cantidades no cumplen el mínimo del 60%';
                }
            }
        }

        // ========== FUNCIÓN PARA CONFIGURAR EVENT LISTENERS ==========

        function configurarEventListenersSolicitud(solicitudDiv) {
            const idSolicitud = solicitudDiv.dataset.id;

            // Event listeners para cambios en cantidades
            solicitudDiv.querySelectorAll('.cantidad-suplir-input').forEach(input => {
                // Remover listeners existentes primero
                const nuevoInput = input.cloneNode(true);
                input.parentNode.replaceChild(nuevoInput, input);

                // Agregar nuevo listener
                nuevoInput.addEventListener('input', function () {
                    const valor = parseInt(this.value) || 0;
                    const cantidadInicial = parseInt(this.dataset.cantidadInicial);
                    const minPermitido = Math.ceil(cantidadInicial * 0.6);

                    if (valor < minPermitido) {
                        this.classList.add('cantidad-input-invalido');
                    } else {
                        this.classList.remove('cantidad-input-invalido');
                    }

                    // Actualizar estado del botón
                    setTimeout(() => actualizarEstadoSolicitud(idSolicitud), 100);
                });

                nuevoInput.addEventListener('change', function () {
                    const numLinea = this.dataset.linea;
                    validarCantidadSuplir(idSolicitud, numLinea, this);
                    actualizarEstadoSolicitud(idSolicitud);
                });
            });

            // Event listeners para cambios en proveedores
            solicitudDiv.querySelectorAll('.proveedor-select').forEach(select => {
                // Remover listeners existentes primero
                const nuevoSelect = select.cloneNode(true);
                select.parentNode.replaceChild(nuevoSelect, select);

                nuevoSelect.addEventListener('change', function () {
                    const numLinea = this.closest('[data-linea]').dataset.linea;
                    cambiarProveedor(idSolicitud, numLinea, this.value);

                    // Actualizar contador y botón
                    actualizarContadorProvAsignados(idSolicitud);
                    actualizarEstadoSolicitud(idSolicitud);
                });
            });
        }

        // ========== FUNCIÓN PARA ACTUALIZAR ESTADO ==========

        function actualizarEstadoSolicitud(idSolicitud) {
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (!solicitudDiv) return;

            const selects = solicitudDiv.querySelectorAll('.proveedor-select');
            const provAsignados = parseInt(solicitudDiv.dataset.provAsignados || 0);

            verificarBotonAprobar(solicitudDiv, provAsignados, selects.length);
        }

        // ========== MODIFICAR LA FUNCIÓN cambiarProveedor ==========

        function cambiarProveedor(idSolicitud, numLinea, rifProveedor) {
            console.log(`Cambiando proveedor: ${idSolicitud}, ${numLinea}, ${rifProveedor}`);

            // Guardar asignación
            if (!asignacionesProveedor[idSolicitud]) {
                asignacionesProveedor[idSolicitud] = {};
            }
            asignacionesProveedor[idSolicitud][numLinea] = rifProveedor;

            // Actualizar contador
            const solicitudDiv = document.querySelector(`.solicitud-card[data-id="${idSolicitud}"]`);
            if (solicitudDiv) {
                let provAsignados = 0;
                solicitudDiv.querySelectorAll('.proveedor-select').forEach(select => {
                    if (select.value && select.value.trim() !== "") {
                        provAsignados++;
                    }
                });

                solicitudDiv.dataset.provAsignados = provAsignados;

                // Actualizar visualmente
                const summaryItems = solicitudDiv.querySelectorAll('.summary-content .summary-item');
                if (summaryItems.length >= 2) {
                    const proveedoresItem = summaryItems[1];
                    const contadorElement = proveedoresItem.querySelector('p');

                    if (contadorElement) {
                        contadorElement.textContent = `${provAsignados} de ${solicitudDiv.querySelectorAll('.proveedor-select').length}`;
                        contadorElement.style.color = provAsignados > 0 ? 'var(--success-color)' : 'var(--warning-color)';
                    }
                }
            }
        }

        // ========== FUNCIÓN PARA CARGAR ASIGNACIONES EXISTENTES ==========

        function cargarAsignacionesExistentes() {
            document.querySelectorAll('.proveedor-select[selected]').forEach(select => {
                const solicitudId = select.closest('.solicitud-card').dataset.id;
                const numLinea = select.closest('[data-linea]').dataset.linea;
                const rifProveedor = select.value;

                if (solicitudId && numLinea && rifProveedor) {
                    cambiarProveedor(solicitudId, numLinea, rifProveedor);

                    // Actualizar estado después de cargar
                    setTimeout(() => {
                        actualizarEstadoSolicitud(solicitudId);
                    }, 500);
                }
            });
        }

        // ========== AGREGAR ESTILOS CSS ==========

        const estilosBotones = document.createElement('style');
        estilosBotones.textContent = `
    .btn-disabled {
        background-color: var(--gray-300) !important;
        border-color: var(--gray-400) !important;
        color: var(--gray-500) !important;
        pointer-events: none;
    }
    
    .btn-success:disabled {
        background-color: var(--gray-300) !important;
        border-color: var(--gray-400) !important;
        color: var(--gray-500) !important;
    }
    
    .cantidad-input-invalido {
        border-color: var(--danger-color) !important;
        background-color: rgba(244, 67, 54, 0.05) !important;
    }
    
    .cantidad-input-invalido:focus {
        box-shadow: 0 0 0 3px rgba(244, 67, 54, 0.1) !important;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s;
    }
`;
        document.head.appendChild(estilosBotones);


        // ========== EVENT LISTENERS ==========

        // Event listener para cuando cambia el tipo de producto (si aplica)
        document.addEventListener('change', function (e) {
            if (e.target.matches('.tipo-producto-select')) {
                const tipo = e.target.value;
                const selectProveedor = e.target.closest('.form-group').nextElementSibling.querySelector('.proveedor-select');
                if (selectProveedor) {
                    selectProveedor.dataset.tipo = tipo;
                    actualizarOpcionesProveedor(selectProveedor, parseInt(tipo));
                }
            }
        });
        // Cerrar modales al hacer clic fuera
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    if (this.id === 'infoModal') {
                        solicitudInfoActual = null;
                    }
                }
            });
        });

        // Cerrar modales con ESC
        document.addEventListener('keydown', function (e) {
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
        // Agregar este CSS al documento
        // Agregar solo estilos esenciales
        const estilosCantidad = document.createElement('style');
        estilosCantidad.textContent = `
    .cantidad-input-invalido {
        border-color: var(--danger-color) !important;
        background-color: rgba(244, 67, 54, 0.05) !important;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s;
    }
`;
        document.head.appendChild(estilosCantidad);
        // Agrega estos estilos al final del script
        const estilosDescartar = document.createElement('style');
        estilosDescartar.textContent = `
    .producto-descartado {
        opacity: 0.6;
        background-color: var(--gray-100) !important;
        border: 2px dashed var(--gray-400) !important;
        pointer-events: none;
        user-select: none;
    }
    
    .producto-descartado .producto-header {
        text-decoration: line-through;
        color: var(--gray-500) !important;
    }
    
    .producto-descartado .btn-descartar-producto {
        display: none !important;
    }
    
    .producto-descartado .proveedor-asignacion {
        display: none !important;
    }
    
    .producto-descartado-badge {
        background-color: var(--danger-color) !important;
        color: white !important;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
    
    .producto-restaurar-btn {
        background: var(--success-color) !important;
        color: white !important;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 0.75rem;
        cursor: pointer;
        margin-top: 0.5rem;
    }
    
    .producto-restaurar-btn:hover {
        background: #2E7D32 !important;
    }
`;
        document.head.appendChild(estilosDescartar);
    </script>
</body>

</html>