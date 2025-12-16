<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'UPEL' ?> - Solicitudes</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/solicitudes.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</head>

<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>

        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1>Gestión de Solicitudes</h1>
                    <?php if ($_SESSION['dpto'] != 4 && $_SESSION['dpto'] != 3): ?>
                        <p class="subtitle">¡Realiza las solicitudes de productos de tu departamento!</p>
                    <?php else: ?>
                        <p class="subtitle">Administra y organiza las solicitudes de productos por departamento</p>
                    <?php endif; ?>
                </div>
                <button class="btn-primary" onclick="openModal()" id="newRequestBtn">
                    <i data-lucide="plus-circle"></i>
                    Nueva Solicitud
                </button>
            </div>

            <!-- Search & Filters -->
            <div class="search-filter-bar">
                <div class="search-box">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Buscar por solicitante, oficina..."
                        aria-label="Buscar solicitudes">
                </div>

                <div class="filter-group">
                    <label for="statusFilter" class="filter-label">Filtrar por estado:</label>
                    <select id="statusFilter" class="filter-select" aria-label="Filtrar por estado">
                        <?php if ($_SESSION['dpto'] != 4 && $_SESSION['dpto'] != 3): ?>
                            <option value="">Todos los estados</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Rechazado">Rechazado</option>
                        <?php else: ?>
                            <?php if ($_SESSION['dpto'] == 4): ?>
                                <option value="En Revisión">En Revisión</option>
                            <?php else: ?>
                                <option value="Pendiente">Pendiente</option>
                            <?php endif; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtro de oficina (solo para dpto != 4) -->
                <?php if ($_SESSION['dpto'] != 4): ?>
                    <div class="filter-group">
                        <label for="oficinaFilter" class="filter-label">Filtrar por oficina:</label>
                        <select id="oficinaFilter" class="filter-select" aria-label="Filtrar por oficina">
                            <option value="">Todas las oficinas</option>
                            <?php if (isset($oficinas['success']) && $oficinas['success'] && !empty($oficinas['data'])): ?>
                                <?php foreach ($oficinas['data'] as $oficina): ?>
                                    <option value="<?php echo $oficina['num_oficina'] ?>">
                                        <?php echo htmlspecialchars($oficina['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="313">Biblioteca</option>
                                <option value="212">Informatica</option>
                                <option value="143">Cuentas</option>
                                <option value="204">Deportes</option>
                                <option value="305">Consejeria/Orientacion</option>
                                <option value="205">Servicios Generales</option>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Filtro Propias/Todas (solo para dpto != 3 y != 4) -->
                <?php if ($_SESSION['dpto'] != 3 && $_SESSION['dpto'] != 4): ?>
                    <div class="filter-group">
                        <label for="tipoSolicitudFilter" class="filter-label">Mostrar:</label>
                        <select id="tipoSolicitudFilter" class="filter-select" aria-label="Filtrar tipo de solicitud">
                            <option value="todas">Todas las solicitudes</option>
                            <option value="propias">Mis solicitudes</option>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="requests-table" id="requestsTable">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Oficina Destino</th>
                            <th>Fecha Deseada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTableBody">
                        <!-- La tabla se llenará dinámicamente con JavaScript -->
                    </tbody>
                </table>

                <div id="emptyState" class="empty-state" style="display: none;">
                    <div class="empty-state-content">
                        <i class="fas fa-inbox empty-icon"></i>
                        <h3>No hay solicitudes</h3>
                        <p>Comienza agregando tu primera solicitud</p>
                        <button class="btn-primary" onclick="openModal('add')">
                            <i class="fas fa-plus"></i>
                            Agregar Solicitud
                        </button>
                    </div>
                </div>

                <!-- Footer Clean Pagination -->
                <div class="pagination-container">
                    <div class="pagination-info" id="paginationInfo">
                        <div class="entries-wrapper">
                            <span>Mostrar</span>
                            <select id="entriesPerPage" class="entries-select-footer" onchange="updatePagination()">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span>registros</span>
                        </div>
                        <span style="margin-left: 1rem; color: #ccc;">|</span>
                        <span id="showingText" style="margin-left: 0.5rem">Mostrando 0-0 de 0</span>
                    </div>
                    <div class="pagination-buttons" id="paginationButtons">
                        <!-- Buttons injected by JS -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Solicitudes -->
    <div id="requestModal" class="modal" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-content" role="document">
            <div class="modal-header">
                <h2 id="modalTitle">Nueva Solicitud</h2>
                <button type="button" class="close-btn" onclick="closeModal()" aria-label="Cerrar modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="requestForm" method="POST" action="?action=solicitudes&method=home"
                onsubmit="return guardarSolicitudConVerificacion(event)">
                <input type="hidden" id="requestId" name="request_id">
                <input type="hidden" id="formMode" name="form_mode" value="add">
                <input type="hidden" id="idSolicitante" name="id_solicitante">

                <div class="modal-body">
                    <div class="form-grid">
                        <?php
                        $departamento = $_SESSION['dpto'];
                        ?>
                        <div class="form-group">
                            <label for="departamento" class="required">Departamento de destino</label>
                            <select id="departamento" name="departamento" class="form-select" required>
                                <?php if (isset($oficinas['success']) && $oficinas['success'] && !empty($oficinas['data'])): ?>
                                    <?php foreach ($oficinas['data'] as $oficina): ?>
                                        <option value="<?php echo $oficina['num_oficina'] ?>" <?php echo $departamento == $oficina['num_oficina'] ? "selected" : "" ?>>
                                            <?php echo $oficina['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="313" <?= $departamento == "Biblioteca" ? "selected" : "" ?>>Biblioteca
                                    </option>
                                    <option value="212" <?= $departamento == "Informatica" ? "selected" : "" ?>>Informatica
                                    </option>
                                    <option value="143" <?= $departamento == "Cuentas" ? "selected" : "" ?>>Cuentas</option>
                                    <option value="204" <?= $departamento == "Deportes" ? "selected" : "" ?>>Deportes</option>
                                    <option value="305" <?= $departamento == "Consejeria/Orientacion" ? "selected" : "" ?>>
                                        Consejeria/Orientacion</option>
                                    <option value="205" <?= $departamento == "Servicios Generales" ? "selected" : "" ?>>
                                        Servicios Generales</option>
                                <?php endif; ?>
                            </select>
                        </div>


                        <!-- Contenedor para grupos de campos de productos -->
                        <div class="product-fields-container" id="productFieldsContainer">
                            <!-- Grupo inicial de campos de producto -->
                            <div class="product-fields-group" data-product-index="0">
                                <div class="product-group-header">
                                    <div class="product-group-title">Producto #1</div>
                                    <!-- Actualiza el HTML de los botones dentro del modal -->
                                    <div class="product-type-buttons" data-index="0">
                                        <button type="button" class="btn-type-selector btn-type-new active"
                                            data-type="new" onclick="seleccionarTipoProducto(0, 'new')">
                                            <i data-lucide="edit-3"></i>
                                            <span class="btn-text">Ingresar Producto</span>
                                            <span class="btn-subtext">Crear producto nuevo</span>
                                        </button>
                                        <button type="button" class="btn-type-selector btn-type-existing"
                                            data-type="existing" onclick="seleccionarTipoProducto(0, 'existing')">
                                            <i data-lucide="package"></i>
                                            <span class="btn-text">Seleccionar Producto</span>
                                            <span class="btn-subtext">Usar producto existente</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="product-fields-grid">
                                    <!-- Campo para seleccionar producto existente (inicialmente oculto) -->
                                    <div class="form-group existing-product-field" id="existingProductGroup_0"
                                        style="display: none; grid-column: span 2;">
                                        <label for="producto_0">Producto existente</label>
                                        <select class="filter-select" name="producto_id[]" id="producto_0"
                                            data-index="0" onchange="actualizarCamposProducto(0)">
                                            <option value="">-- Seleccionar producto existente --</option>
                                            <?php foreach ($productos['data'] as $producto): ?>
                                                <option value="<?php echo $producto['id_producto'] ?>"
                                                    data-nombre="<?php echo htmlspecialchars($producto['nombre']) ?>"
                                                    data-unidad="<?php echo htmlspecialchars($producto['medida'] ?? '') ?>"
                                                    data-tipo="<?php echo $producto['id_tipo'] ?? '' ?>">
                                                    <?php echo $producto['nombre'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nombre_producto_0" class="required">Nombre del Producto</label>
                                        <input type="text" id="nombre_producto_0" name="nombre_producto[]" required
                                            placeholder="Ingrese el nombre del producto" minlength="2">
                                    </div>

                                    <div class="form-group">
                                        <label for="unidad_medida_0" class="required">Unidad de Medida</label>
                                        <select id="unidad_medida_0" name="unidad_medida[]" required minlength="2">
                                            <option value="">Seleccionar unidad</option>
                                            <option value="Unidades">Unidades</option>
                                            <option value="Kilogramos">Kilogramos</option>
                                            <option value="Litros">Litros</option>
                                            <option value="Cajas">Cajas</option>
                                            <option value="Paquetes">Paquetes</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="cantidad_0" class="required">Cantidad</label>
                                        <input type="number" id="cantidad_0" name="cantidad[]" required
                                            placeholder="Ej: 10" min="1" max="9999">
                                    </div>

                                    <div class="form-group">
                                        <label for="tipo_producto_0" class="required">Tipo de Producto</label>
                                        <select id="tipo_producto_0" name="tipo_producto[]" class="form-select-sm">
                                            <?php if (isset($tiposProducto['success']) && $tiposProducto['success'] && !empty($tiposProducto['data'])): ?>
                                                <?php foreach ($tiposProducto['data'] as $tipo): ?>
                                                    <option value="<?php echo $tipo['id_tipo'] ?>"><?php echo $tipo['nombre'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="Alimento" selected>Alimento</option>
                                                <option value="Limpieza">Limpieza</option>
                                                <option value="Electronicos">Electronicos</option>
                                                <option value="Oficina">Oficina</option>
                                                <option value="Material literario">Material literario</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Botón de eliminar para grupos adicionales -->
                                <button type="button" class="remove-product-btn" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="add-product-btn" id="addProductBtn">
                            <i class="fas fa-plus"></i>
                            Añadir otro producto
                        </button>

                        <div class="final-fields-container">
                            <div class="form-group">
                                <label for="fecha_requerida" class="required">Fecha deseada de entrega</label>
                                <input type="date" id="fecha_requerida" name="fecha_requerida" required
                                    min="<?= date('Y-m-d') ?>">
                            </div>

                            <div></div> <!-- Espacio vacío para mantener el grid -->
                        </div>

                        <!-- Notas Adicionales (ocupa todo el ancho) -->
                        <div class="form-group">
                            <label for="notas">Notas Adicionales</label>
                            <textarea id="notas" name="notas" rows="3"
                                placeholder="Información adicional sobre la solicitud, justificación, urgencia, etc..."
                                maxlength="500"></textarea>
                            <div class="char-count">
                                <span id="charCount">0</span>/500 caracteres
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <span class="btn-text">Guardar Solicitud</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variable global para productos (convertir PHP a JavaScript)
        const productosData = <?= json_encode($productos['data'] ?? []) ?>;
        const solicitudesData = <?= json_encode($solicitudes) ?>;
        const userRol = <?= json_encode($_SESSION['dpto'] ?? '') ?>;
        const userId = <?= json_encode($_SESSION['id'] ?? '') ?>;
        const oficinasData = <?= json_encode($oficinas['data'] ?? []) ?>;
        console.log(solicitudesData)

        // Variables para paginación y filtrado
        let currentPage = 1;
        let entriesPerPage = 10;
        let filteredSolicitudes = [...solicitudesData]; // Copia de todas las solicitudes

        // Inicializar filtros y paginación al cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            var error = "<?= isset($_GET['error']) ?>";
            // Verificamos si está seteado y no vacío
            if (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se agregó la solicitud',
                    text: 'La solicitud tuvo problemas al agregarse',
                    confirmButtonText: 'Entendido'
                });
            }
            updateProductsCounter();
            initializeFilters();
            updateTable(); // ¡IMPORTANTE! Renderizar la tabla inicialmente
            // Asegurar que el primer producto tenga "Ingresar Producto" seleccionado por defecto
            seleccionarTipoProducto(0, 'new');

            // Inicializar contador de caracteres
            document.getElementById('notas').addEventListener('input', function () {
                document.getElementById('charCount').textContent = this.value.length;
            });
        });
        async function procederConGuardado(formMode, productosActualizados = null) {
            const form = document.getElementById('requestForm');
            const requestId = document.getElementById('requestId').value;

            try {
                // Si es modo editar, usar AJAX
                if (formMode === 'edit') {
                    return await guardarEdicion(requestId, productosActualizados);
                } else {
                    // Modo agregar - usar submit tradicional o AJAX
                    return await guardarNuevaSolicitud(form);
                }
            } catch (error) {
                swalLoading.close();
                console.error('Error en procederConGuardado:', error);
                mostrarErrorGuardado(error);
                return false;
            }
        }

        // Función para guardar en modo edición
        async function guardarEdicion(requestId, productos) {
            closeModal();
            // Mostrar loading
            const swalLoading = Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Obtener el ID del solicitante del campo oculto
                const idSolicitante = document.getElementById('idSolicitante').value;

                // Crear FormData
                const formData = new FormData();

                // Datos básicos del formulario
                formData.append('request_id', requestId);
                formData.append('departamento', document.getElementById('departamento').value);
                formData.append('fecha_requerida', document.getElementById('fecha_requerida').value);
                formData.append('notas', document.getElementById('notas').value);
                formData.append('form_mode', 'edit');
                formData.append('id_solicitante', idSolicitante);

                // Agregar productos
                const productosParaEnviar = prepararProductosParaEnvio(productos);
                productosParaEnviar.forEach((prod, index) => {
                    formData.append(`producto_id[]`, prod.id_producto || '');
                    formData.append(`nombre_producto[]`, prod.nombre_producto || '');
                    formData.append(`unidad_medida[]`, prod.unidad_medida || '');
                    formData.append(`cantidad[]`, prod.cantidad || '');
                    formData.append(`tipo_producto[]`, prod.tipo_producto || '');
                });

                // NOTA: NO cambiar estado aquí. Eso debe ser una acción separada.

                // Enviar a actualizarSolic
                const response = await fetch('?action=solicitudes&method=actualizarSolic', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                swalLoading.close();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Solicitud actualizada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    closeModal();
                    location.reload();
                    return true;
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al actualizar la solicitud'
                    });
                    return false;
                }
            } catch (error) {
                swalLoading.close();
                console.error('Error:', error);
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la solicitud'
                });
                return false;
            }
        }
        function prepararProductosParaEnvio(productos) {
            return productos.map(prod => {
                // Convertir del formato interno al formato que espera el backend
                return {
                    id_producto: prod.id_producto || '',
                    nombre_producto: prod.nombre || '',
                    unidad_medida: prod.medida || '',
                    cantidad: prod.cantidad || '',
                    tipo_producto: prod.id_tipo || ''
                };
            });
        }
        // Nueva función para combinar cambio de estado con productos
        async function cambiarEstadoConProductos(solicitudId, nuevoEstado, productos) {
            return new Promise((resolve) => {
                // Primero preguntar confirmación
                let mensajeConfirmacion = '';
                let tituloConfirmacion = '';

                if (userRol == 4 || userRol == 1) {
                    tituloConfirmacion = '¿Aprobar solicitud?';
                    mensajeConfirmacion = 'Si la apruebas, será enviada al departamento de Presupuesto. ¿Deseas continuar?';
                } else if (userRol == 3) {
                    tituloConfirmacion = '¿Enviar a revisión?';
                    mensajeConfirmacion = 'Si la envías a revisión, será evaluada por el departamento de Presupuesto. ¿Deseas continuar?';
                }

                Swal.fire({
                    title: tituloConfirmacion,
                    text: mensajeConfirmacion,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        const swalLoading = Swal.fire({
                            title: 'Procesando...',
                            text: 'Actualizando solicitud',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        try {
                            // Crear FormData con todos los datos
                            const formData = new FormData();

                            // Datos básicos
                            formData.append('id_solicitud', solicitudId);
                            formData.append('nuevo_estado', nuevoEstado);
                            formData.append('form_mode', 'edit');

                            // Campos del formulario
                            formData.append('departamento', document.getElementById('departamento').value);
                            formData.append('fecha_requerida', document.getElementById('fecha_requerida').value);
                            formData.append('notas', document.getElementById('notas').value);

                            // Productos
                            formData.append('productos', JSON.stringify(productos));

                            // Enviar todo junto
                            const response = await fetch('?action=solicitudes&method=actualizarSolic', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.json();
                            swalLoading.close();

                            if (result.success) {
                                let mensaje = '';
                                if (userRol == 4 || userRol == 1) {
                                    mensaje = 'Solicitud aprobada correctamente';
                                } else if (userRol == 3) {
                                    mensaje = 'Solicitud enviada a revisión correctamente';
                                }

                                await Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: mensaje,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                closeModal();
                                location.reload();
                                resolve(true);
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message || 'Error al actualizar la solicitud'
                                });
                                resolve(false);
                            }
                        } catch (error) {
                            swalLoading.close();
                            console.error('Error:', error);
                            await Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al guardar la solicitud'
                            });
                            resolve(false);
                        }
                    } else {
                        resolve(false); // Usuario canceló
                    }
                });
            });
        }

        // Función para guardar nueva solicitud
        async function guardarNuevaSolicitud(form) {
            // Asegurar id_solicitante antes de crear FormData
            const idSolicitanteField = document.getElementById('idSolicitante');
            if (!idSolicitanteField.value) {
                idSolicitanteField.value = userId;
            }

            // Habilitar temporalmente campos deshabilitados
            const disabledFields = form.querySelectorAll('input:disabled, select:disabled');
            disabledFields.forEach(field => {
                field.disabled = false;
            });

            // Crear FormData manualmente
            const formData = new FormData();
            const formElements = form.elements;

            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (element.name && !element.disabled) {
                    if (element.type === 'checkbox' || element.type === 'radio') {
                        if (element.checked) {
                            formData.append(element.name, element.value);
                        }
                    } else {
                        if (element.value || element.value === '0') {
                            formData.append(element.name, element.value);
                        }
                    }
                }
            }

            // Debug
            console.log('Enviando nueva solicitud con id_solicitante:', idSolicitanteField.value);

            try {
                const response = await fetch('?action=solicitudes&method=home', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();

                if (response.ok && !text.includes('error') && !text.includes('Error')) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Solicitud creada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    closeModal();
                    location.reload();
                    return true;
                } else {
                    throw new Error('Error en la respuesta del servidor');
                }
            } catch (error) {
                // Fallback: enviar formulario tradicionalmente
                console.log('Usando fallback tradicional');

                // Asegurar id_solicitante en el formulario
                idSolicitanteField.value = userId;

                Swal.close();
                form.submit();
                return true;
            }
        }

        // Función auxiliar para recopilar productos del formulario
        function recopilarProductosDelFormulario() {
            const productGroups = document.querySelectorAll('.product-fields-group');
            const productos = [];

            productGroups.forEach((group, index) => {
                const tipo = group.querySelector('.product-type-buttons .active')?.dataset.type;
                const producto = { tipo: tipo };

                if (tipo === 'existing') {
                    const productoSelect = document.getElementById(`producto_${index}`);
                    if (productoSelect) {
                        producto.id_producto = productoSelect.value;

                        // Obtener datos del option seleccionado
                        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
                        if (selectedOption) {
                            producto.nombre = selectedOption.getAttribute('data-nombre') || '';
                            producto.medida = selectedOption.getAttribute('data-unidad') || '';
                            producto.id_tipo = selectedOption.getAttribute('data-tipo') || '';
                        }
                    }
                } else {
                    const nombreInput = document.getElementById(`nombre_producto_${index}`);
                    const unidadSelect = document.getElementById(`unidad_medida_${index}`);
                    const tipoSelect = document.getElementById(`tipo_producto_${index}`);

                    if (nombreInput) producto.nombre = nombreInput.value.trim();
                    if (unidadSelect) producto.medida = unidadSelect.value;
                    if (tipoSelect) producto.id_tipo = tipoSelect.value;
                }

                const cantidadInput = document.getElementById(`cantidad_${index}`);
                if (cantidadInput) producto.cantidad = cantidadInput.value;

                productos.push(producto);
            });

            return productos;
        }
        function seleccionarTipoProducto(index, tipo) {
            console.log(`Seleccionando tipo ${tipo} para producto ${index}`);

            const existingProductGroup = document.getElementById(`existingProductGroup_${index}`);
            const productSelect = document.getElementById(`producto_${index}`);
            const nombreInput = document.getElementById(`nombre_producto_${index}`);
            const unidadSelect = document.getElementById(`unidad_medida_${index}`);
            const tipoSelect = document.getElementById(`tipo_producto_${index}`);
            const cantidadInput = document.getElementById(`cantidad_${index}`);

            // Obtener botones de tipo
            const buttonsContainer = document.querySelector(`.product-type-buttons[data-index="${index}"]`);
            const newProductBtn = buttonsContainer.querySelector('[data-type="new"]');
            const existingProductBtn = buttonsContainer.querySelector('[data-type="existing"]');

            // Actualizar estado de botones
            newProductBtn.classList.toggle('active', tipo === 'new');
            existingProductBtn.classList.toggle('active', tipo === 'existing');

            if (tipo === 'existing') {
                // Mostrar select de productos existentes
                if (existingProductGroup) {
                    existingProductGroup.style.display = 'block';
                }

                // Deshabilitar y limpiar los otros campos
                if (nombreInput) {
                    nombreInput.disabled = true;
                    nombreInput.required = false;
                    nombreInput.value = '';
                }

                if (unidadSelect) {
                    unidadSelect.disabled = true;
                    unidadSelect.required = false;
                    unidadSelect.value = '';
                }

                if (tipoSelect) {
                    tipoSelect.disabled = true;
                    tipoSelect.value = '';
                }

                // Asegurar que el select de producto esté habilitado
                if (productSelect) {
                    productSelect.disabled = false;
                    productSelect.required = true;
                }

            } else {
                // Ocultar select de productos existentes
                if (existingProductGroup) {
                    existingProductGroup.style.display = 'none';
                }

                // Habilitar los otros campos
                if (nombreInput) {
                    nombreInput.disabled = false;
                    nombreInput.required = true;
                }

                if (unidadSelect) {
                    unidadSelect.disabled = false;
                    unidadSelect.required = true;
                }

                if (tipoSelect) {
                    tipoSelect.disabled = false;
                }

                // Deshabilitar y limpiar el select de producto
                if (productSelect) {
                    productSelect.disabled = true;
                    productSelect.required = false;
                    productSelect.value = '';
                }

                // Si estamos editando y había un producto seleccionado, limpiarlo
                const productGroup = document.querySelector(`.product-fields-group[data-product-index="${index}"]`);
                if (productGroup) {
                    productGroup.dataset.productType = 'new';
                    productGroup.dataset.productId = '';
                }
            }
        }

        // Inicializar filtros
        function initializeFilters() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const entriesPerPageSelect = document.getElementById('entriesPerPage');
            const oficinaFilter = document.getElementById('oficinaFilter');
            const tipoSolicitudFilter = document.getElementById('tipoSolicitudFilter');

            // Filtrar al escribir en la búsqueda
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyFilters();
                });
            }

            // Filtrar al cambiar estado
            if (statusFilter) {
                statusFilter.addEventListener('change', function () {
                    applyFilters();
                });
            }

            // Filtrar por oficina (si existe el filtro)
            if (oficinaFilter) {
                oficinaFilter.addEventListener('change', function () {
                    applyFilters();
                });
            }

            // Filtrar por tipo de solicitud (propias/todas) (si existe el filtro)
            if (tipoSolicitudFilter) {
                tipoSolicitudFilter.addEventListener('change', function () {
                    applyFilters();
                });
            }

            // Cambiar cantidad de registros por página
            if (entriesPerPageSelect) {
                entriesPerPageSelect.addEventListener('change', function () {
                    entriesPerPage = parseInt(this.value);
                    currentPage = 1; // Volver a la primera página
                    updateTable();
                });
            }
        }

        // Aplicar todos los filtros
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilterValue = document.getElementById('statusFilter').value;
            const oficinaFilter = document.getElementById('oficinaFilter');
            const tipoSolicitudFilter = document.getElementById('tipoSolicitudFilter');

            const oficinaFilterValue = oficinaFilter ? oficinaFilter.value : '';
            const tipoSolicitudValue = tipoSolicitudFilter ? tipoSolicitudFilter.value : '';

            console.log('Aplicando filtros:', {
                searchTerm,
                statusFilterValue,
                oficinaFilterValue,
                tipoSolicitudValue
            });

            // Filtrar solicitudes
            filteredSolicitudes = solicitudesData.filter(solicitud => {
                let matches = true;

                // Filtrar por búsqueda (solicitante u oficina)
                if (searchTerm) {
                    const searchInSolicitante = solicitud.nombre_solicitante?.toLowerCase().includes(searchTerm);
                    const searchInOficina = solicitud.nombre_oficina?.toLowerCase().includes(searchTerm);
                    matches = matches && (searchInSolicitante || searchInOficina);
                }

                // Filtrar por estado
                if (statusFilterValue) {
                    matches = matches && (solicitud.estado === statusFilterValue);
                }

                // Filtrar por oficina (solo si el filtro existe y hay valor)
                if (oficinaFilterValue) {
                    matches = matches && (solicitud.num_oficina == oficinaFilterValue);
                }

                // Filtrar por tipo de solicitud (propias/todas)
                if (tipoSolicitudValue === 'propias') {
                    // Mostrar solo las solicitudes del usuario actual
                    matches = matches && (solicitud.id_solicitante == userId);
                }
                // Si es 'todas', no se aplica filtro adicional

                return matches;
            });

            console.log('Solicitudes filtradas:', filteredSolicitudes.length);

            currentPage = 1; // Volver a la primera página
            updateTable();
        }

        // Actualizar la tabla con los datos filtrados
        function updateTable() {
            const tableBody = document.getElementById('requestsTableBody');
            const emptyState = document.getElementById('emptyState');
            const showingText = document.getElementById('showingText');
            const paginationButtons = document.getElementById('paginationButtons');

            if (!tableBody) {
                console.error('No se encontró el elemento requestsTableBody');
                return;
            }

            console.log('Actualizando tabla:', {
                total: filteredSolicitudes.length,
                currentPage: currentPage,
                entriesPerPage: entriesPerPage
            });

            // Si no hay solicitudes filtradas, mostrar estado vacío
            if (filteredSolicitudes.length === 0) {
                tableBody.innerHTML = '';
                if (emptyState) emptyState.style.display = 'flex';
                if (showingText) showingText.textContent = 'Mostrando 0-0 de 0';
                if (paginationButtons) paginationButtons.innerHTML = '';
                return;
            }

            // Ocultar estado vacío
            if (emptyState) emptyState.style.display = 'none';

            // Calcular índices de paginación
            const totalItems = filteredSolicitudes.length;
            const totalPages = Math.ceil(totalItems / entriesPerPage);
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = Math.min(startIndex + entriesPerPage, totalItems);

            // Obtener los elementos para la página actual
            const currentPageItems = filteredSolicitudes.slice(startIndex, endIndex);

            // Generar HTML para la tabla
            let tableHTML = '';

            currentPageItems.forEach(solicitud => {
                // Determinar icono según estado
                let iconName = 'clock';
                if (solicitud.estado === 'En Revisión') iconName = 'pencil';
                if (solicitud.estado === 'Rechazado') iconName = 'x-circle';
                if (solicitud.estado === 'Aprobado') iconName = 'check-circle';

                // Formatear fecha
                const fechaFormateada = solicitud.fecha_deseo ?
                    new Date(solicitud.fecha_deseo).toLocaleDateString('es-ES') : 'N/A';

                // Determinar qué botones mostrar según el rol Y el estado
                let actionButtons = '';

                // Solo mostrar botones de editar/rechazar si el estado es apropiado
                const mostrarEditar = (solicitud.estado === 'Pendiente' || solicitud.estado === 'En Revisión');

                // En la sección donde generas los botones de acción:
                
                if ((userRol == 3 && mostrarEditar) || (userRol == 1 && mostrarEditar)) {
                    // Rol 3 o 1: Enviar a revisión o Aprobar
                    // Botón para enviar a revisión (Rol 3)
                    actionButtons = `
        <button type="button" class="btn-action edit" 
                onclick="cambiarEstadoSolicitud(${solicitud.id_solicitud}, 'En Revisión')"
                data-tippy-content="Enviar a Revisión">
            <i data-lucide="send"></i>
        </button>`;
                    // Botón de rechazo para ambos
                    actionButtons += `
    <button type="button" class="btn-action reject" 
            onclick="rechazarSolicitud(${solicitud.id_solicitud})"
            data-tippy-content="Rechazar">
        <i data-lucide="x"></i>
    </button>
    <button type="button" class="btn-action view" 
            onclick="verDetallesSolicitudFiltrada(${solicitud.id_solicitud})"
            data-tippy-content="Ver Detalles">
        <i data-lucide="eye"></i>
    </button>`;
                } else if (userRol == 4 && mostrarEditar) {
                    // Rol 4: Aprobar directamente
                    if (solicitud.estado === 'Pendiente' || solicitud.estado === 'En Revisión') {
                        actionButtons = `
        <button type="button" class="btn-action approve" 
                onclick="cambiarEstadoSolicitud(${solicitud.id_solicitud}, 'Aprobado')"
                data-tippy-content="Aprobar">
            <i data-lucide="check"></i>
        </button>`;
                    }

                    actionButtons += `
    <button type="button" class="btn-action reject" 
            onclick="rechazarSolicitud(${solicitud.id_solicitud})"
            data-tippy-content="Rechazar">
        <i data-lucide="x"></i>
    </button>`;
                } else {
                    // Otros departamentos
                    if (mostrarEditar && solicitud.id_solicitante == userId) {
                        actionButtons += `
        <button type="button" class="btn-action edit" 
                onclick="editarSolicitud(${solicitud.id_solicitud})"
                data-tippy-content="Editar">
            <i data-lucide="pencil"></i>
        </button>`;
                    }
                    actionButtons += `
    <button type="button" class="btn-action view" 
            onclick="verDetallesSolicitudFiltrada(${solicitud.id_solicitud})"
            data-tippy-content="Ver Detalles">
        <i data-lucide="eye"></i>
    </button>`;
                }

                tableHTML += `
                <tr data-status="${solicitud.estado || ''}" 
                    data-id="${solicitud.id_solicitud || ''}"
                    class="request-row">
                    <td>
                        <strong>${solicitud.nombre_solicitante || 'N/A'}</strong>
                    </td>
                    <td>${solicitud.nombre_oficina || 'N/A'}</td>
                    <td>${fechaFormateada}</td>
                    <td>
                        <span class="status-badge status-${(solicitud.estado || '').toLowerCase().replace(' ', '-')}">
                            <i data-lucide="${iconName}" style="width:14px; height:14px;"></i>
                            ${solicitud.estado || 'N/A'}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
            });

            tableBody.innerHTML = tableHTML;

            // Actualizar información de paginación
            if (showingText) {
                showingText.textContent = `Mostrando ${startIndex + 1}-${endIndex} de ${totalItems}`;
            }

            // Generar botones de paginación
            if (paginationButtons) {
                paginationButtons.innerHTML = generatePaginationButtons(totalPages);
            }

            // Activar iconos de Lucide
            lucide.createIcons();

            console.log('Tabla actualizada con', currentPageItems.length, 'elementos');
        }

        // Nueva función para ver detalles de solicitudes filtradas
        function verDetallesSolicitudFiltrada(idSolicitud) {
            // Encontrar la solicitud en los datos filtrados
            const solicitud_seleccionada = filteredSolicitudes.find(s => s.id_solicitud == idSolicitud);

            if (!solicitud_seleccionada) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontró la solicitud en los datos filtrados'
                });
                return;
            }

            verDetallesSolicitud(filteredSolicitudes, idSolicitud);
        }

        // Generar botones de paginación
        function generatePaginationButtons(totalPages) {
            if (totalPages <= 1) return '';

            let buttons = '';

            // Botón anterior
            buttons += `
            <button class="pagination-btn ${currentPage === 1 ? 'disabled' : ''}" 
                    ${currentPage === 1 ? 'disabled' : ''}
                    onclick="changePage(${currentPage - 1})">
                <i data-lucide="chevron-left"></i>
            </button>
        `;

            // Botones de páginas
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            // Ajustar si estamos cerca del final
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // Página 1
            if (startPage > 1) {
                buttons += `
                <button class="pagination-btn" onclick="changePage(1)">1</button>
                ${startPage > 2 ? '<span class="pagination-ellipsis">...</span>' : ''}
            `;
            }

            // Páginas intermedias
            for (let i = startPage; i <= endPage; i++) {
                buttons += `
                <button class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                        onclick="changePage(${i})">
                    ${i}
                </button>
            `;
            }

            // Última página
            if (endPage < totalPages) {
                buttons += `
                ${endPage < totalPages - 1 ? '<span class="pagination-ellipsis">...</span>' : ''}
                <button class="pagination-btn" onclick="changePage(${totalPages})">
                    ${totalPages}
                </button>
            `;
            }

            // Botón siguiente
            buttons += `
            <button class="pagination-btn ${currentPage === totalPages ? 'disabled' : ''}" 
                    ${currentPage === totalPages ? 'disabled' : ''}
                    onclick="changePage(${currentPage + 1})">
                <i data-lucide="chevron-right"></i>
            </button>
        `;

            return buttons;
        }

        // Cambiar de página
        function changePage(page) {
            const totalPages = Math.ceil(filteredSolicitudes.length / entriesPerPage);

            if (page < 1 || page > totalPages || page === currentPage) {
                return;
            }

            currentPage = page;
            updateTable();

            // Hacer scroll suave hacia la parte superior de la tabla
            const tableContainer = document.querySelector('.table-container');
            if (tableContainer) {
                tableContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        // Función para actualizar paginación (llamada desde el select)
        function updatePagination() {
            const select = document.getElementById('entriesPerPage');
            if (select) {
                entriesPerPage = parseInt(select.value);
                currentPage = 1;
                updateTable();
            }
        }

        // Resto del código JavaScript (funciones del modal) permanece igual...
        // Funciones básicas para el modal
        function openModal(mode = 'add') {
            const modal = document.getElementById('requestModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('requestForm');
            const formMode = document.getElementById('formMode');

            if (mode === 'add') {
                modalTitle.textContent = 'Nueva Solicitud';
                formMode.value = 'add';
                form.reset();
                limpiarCamposProductos();

                document.getElementById('idSolicitante').value = userId;

                // Establecer fecha mínima como hoy
                const today = new Date();
                document.getElementById('fecha_requerida').min = today.toISOString().split('T')[0];

                // Establecer fecha por defecto (7 días en el futuro)
                const nextWeek = new Date(today);
                nextWeek.setDate(today.getDate() + 7);
                document.getElementById('fecha_requerida').valueAsDate = nextWeek;

                // Inicializar contador de caracteres
                document.getElementById('notas').addEventListener('input', function () {
                    document.getElementById('charCount').textContent = this.value.length;
                });

            } else if (mode === 'edit') {
                // Cambiar título según el rol al editar
                if (userRol == 3) {
                    modalTitle.textContent = 'Enviar Solicitud a Revisión';
                } else {
                    modalTitle.textContent = 'Editar Solicitud';
                }

                formMode.value = 'edit';
                // Los datos se cargan cuando se llama a editarSolicitud()
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('requestModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';

            // Resetear a modo agregar
            const form = document.getElementById('requestForm');
            form.reset();
            document.getElementById('formMode').value = 'add';
            document.getElementById('modalTitle').textContent = 'Nueva Solicitud';

            // Restablecer a un solo grupo de productos
            const container = document.getElementById('productFieldsContainer');
            while (container.children.length > 1) {
                container.removeChild(container.lastChild);
            }

            // Actualizar índices y contador
            updateProductIndexes();
            updateProductsCounter();
        }

        // Cerrar modal al hacer clic fuera del contenido
        document.getElementById('requestModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Función para editar solicitud
        async function editarSolicitud(solicitudId) {
            try {
                // Mostrar carga
                Swal.fire({
                    title: 'Cargando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Encontrar la solicitud en los datos locales
                const solicitudLocal = solicitudesData.find(s => s.id_solicitud == solicitudId);

                if (!solicitudLocal) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se encontró la solicitud'
                    });
                    return;
                }

                // Crear el body con los datos que espera el backend
                const body = new URLSearchParams();
                body.append('id_solicitud', solicitudId);
                body.append('solicitud_seleccionada', JSON.stringify(solicitudLocal));

                // Obtener detalles de la solicitud con productos
                const response = await fetch('?action=solicitudes&method=obtenerDetalles', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body
                });

                Swal.close();

                if (!response.ok) {
                    throw new Error("Error en la petición: " + response.status);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    console.log('Datos completos recibidos:', result.data);
                    openModal('edit');
                    cargarSolicitudParaEditar(result.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'No se pudo cargar la solicitud para editar'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos de la solicitud'
                });
            }
        }

        // Función para cargar los datos de una solicitud en el formulario
        function cargarSolicitudParaEditar(solicitud) {
            console.log('Cargando solicitud para editar:', solicitud);

            // Cargar datos básicos
            document.getElementById('requestId').value = solicitud.id_solicitud || '';
            document.getElementById('departamento').value = solicitud.num_oficina || '';
            document.getElementById('fecha_requerida').value = solicitud.fecha_deseo || '';
            document.getElementById('notas').value = solicitud.comentarios || '';
            document.getElementById('idSolicitante').value = solicitud.id_solicitante || userId;

            // Actualizar contador de caracteres
            const charCount = document.getElementById('charCount');
            if (charCount) {
                charCount.textContent = (solicitud.comentarios || '').length;
            }

            // Limpiar campos de productos existentes
            limpiarCamposProductos();

            // Si hay productos, cargarlos
            if (solicitud.productos && Array.isArray(solicitud.productos) && solicitud.productos.length > 0) {
                console.log(`Creando ${solicitud.productos.length} grupos de productos`);

                // Crear grupos adicionales si hay más de un producto
                for (let i = 1; i < solicitud.productos.length; i++) {
                    addProductFields();
                }

                // Ahora llenar todos los grupos con los datos
                solicitud.productos.forEach((producto, index) => {
                    console.log(`Cargando producto ${index}:`, producto);

                    // Determinar el tipo de producto (existente o nuevo)
                    const tieneProductoId = producto.id_producto && producto.id_producto !== '';
                    const tipo = tieneProductoId ? 'existing' : 'new';

                    // Seleccionar tipo de producto
                    seleccionarTipoProducto(index, tipo);

                    // Obtener referencias a los elementos
                    const productoSelect = document.getElementById(`producto_${index}`);
                    const nombreInput = document.getElementById(`nombre_producto_${index}`);
                    const unidadSelect = document.getElementById(`unidad_medida_${index}`);
                    const cantidadInput = document.getElementById(`cantidad_${index}`);
                    const tipoSelect = document.getElementById(`tipo_producto_${index}`);

                    // Llenar campos según el tipo
                    if (tipo === 'existing' && productoSelect && producto.id_producto) {
                        productoSelect.value = producto.id_producto;
                        // Forzar la actualización de campos
                        setTimeout(() => actualizarCamposProducto(index), 100);
                    }

                    if (nombreInput) {
                        nombreInput.value = producto.nombre || '';
                    }

                    if (unidadSelect && producto.medida) {
                        console.log("Colocando valor de unidad de medida...")
                        unidadSelect.value = producto.medida;
                    }

                    if (cantidadInput) {
                        cantidadInput.value = producto.un_deseadas || '';
                    }

                    if (tipoSelect && producto.id_tipo) {
                        tipoSelect.value = producto.id_tipo;
                    }
                });
            }
        }
        // Función para limpiar todos los campos de productos
        function limpiarCamposProductos() {
            const container = document.getElementById('productFieldsContainer');
            // Mantener solo el primer grupo
            while (container.children.length > 1) {
                container.removeChild(container.lastChild);
            }

            // Limpiar el primer grupo
            const firstGroup = container.querySelector('.product-fields-group');
            if (firstGroup) {
                const inputs = firstGroup.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.type === 'text' || input.type === 'number') {
                        input.value = '';
                    } else if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    }
                });
            }

            updateProductIndexes();
            updateProductsCounter();
        }

        // Agregar CSS para los botones de tipo
        const productTypeStyles = document.createElement('style');
        productTypeStyles.textContent = `
    .product-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        grid-column: span 2;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .product-group-title {
        font-weight: 600;
        color: #ffffffff;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .product-type-buttons {
        display: flex;
        gap: 1rem;
    }
    
    .btn-type-selector {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 1rem 1.5rem;
        border: 2px solid;
        background: white;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        min-width: 180px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .btn-type-selector::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: currentColor;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .btn-type-selector:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.15), 0 3px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-type-selector:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.1s ease;
    }
    
    .btn-type-selector i {
        width: 24px;
        height: 24px;
        stroke-width: 1.5;
        margin-bottom: 0.25rem;
    }
    
    /* Botón "Ingresar Producto" */
    .btn-type-new {
        border-color: #3498db;
        color: #3498db;
    }
    
    .btn-type-new:hover {
        background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
        border-color: #2980b9;
        color: #2980b9;
    }
    
    .btn-type-new.active {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border-color: #2980b9;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
    
    .btn-type-new.active::before {
        opacity: 1;
        background: #1f618d;
    }
    
    .btn-type-new.active:hover {
        background: linear-gradient(135deg, #2980b9 0%, #1f618d 100%);
        border-color: #1f618d;
        box-shadow: 0 7px 20px rgba(52, 152, 219, 0.4);
    }
    
    /* Botón "Seleccionar Producto" */
    .btn-type-existing {
        border-color: #2ecc71;
        color: #2ecc71;
    }
    
    .btn-type-existing:hover {
        background: linear-gradient(135deg, #f0fff4 0%, #e8f5e9 100%);
        border-color: #27ae60;
        color: #27ae60;
    }
    
    .btn-type-existing.active {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        color: white;
        border-color: #27ae60;
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
    }
    
    .btn-type-existing.active::before {
        opacity: 1;
        background: #219653;
    }
    
    .btn-type-existing.active:hover {
        background: linear-gradient(135deg, #27ae60 0%, #219653 100%);
        border-color: #219653;
        box-shadow: 0 7px 20px rgba(46, 204, 113, 0.4);
    }
    
    /* Texto de los botones */
    .btn-text {
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }
    
    .btn-subtext {
        font-size: 0.75rem;
        opacity: 0.8;
        max-width: 120px;
        line-height: 1.2;
    }
    
    .btn-type-selector.active .btn-subtext {
        opacity: 0.9;
        color: rgba(255, 255, 255, 0.9);
    }
    
    /* Efecto de pulso para indicar selección */
    @keyframes pulse-selected {
        0% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
        100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0); }
    }
    
    .btn-type-new.active {
        animation: pulse-selected 2s infinite;
    }
    
    @keyframes pulse-selected-green {
        0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
    }
    
    .btn-type-existing.active {
        animation: pulse-selected-green 2s infinite;
    }
    
    /* Indicador de selección */
    .btn-type-selector.active::after {
        content: "✓";
        position: absolute;
        top: 8px;
        right: 8px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .btn-type-new.active::after {
        color: #3498db;
    }
    
    .btn-type-existing.active::after {
        color: #2ecc71;
    }
    
    /* Campos deshabilitados */
    input:disabled, select:disabled {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        border-color: #dee2e6 !important;
        opacity: 0.7;
    }
    
    /* Campos habilitados */
    input:not(:disabled), select:not(:disabled) {
        background-color: white !important;
        border-color: #ced4da !important;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    
    input:not(:disabled):focus, select:not(:disabled):focus {
        border-color: #3498db !important;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25) !important;
    }
    
    /* Grupo de campos de productos */
    .product-fields-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        grid-column: span 2;
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        margin-top: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    /* Campo de producto existente */
    .existing-product-field {
        transition: all 0.3s ease;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Efecto cuando el grupo está en modo "existente" */
    .product-fields-group[data-product-type="existing"] .product-fields-grid {
        background: linear-gradient(135deg, #f8fff9 0%, #f0f9f0 100%);
        border-color: #d1e7dd;
    }
    
    /* Efecto cuando el grupo está en modo "new" */
    .product-fields-group[data-product-type="new"] .product-fields-grid {
        background: linear-gradient(135deg, #f8fbff 0%, #f0f8ff 100%);
        border-color: #d1e3f5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .product-group-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }
        
        .product-type-buttons {
            flex-direction: column;
        }
        
        .btn-type-selector {
            min-width: auto;
            width: 100%;
        }
        
        .product-fields-grid {
            grid-template-columns: 1fr;
        }
    }
`;
        document.head.appendChild(productTypeStyles);

        // Función para actualizar campos cuando se selecciona un producto existente
        function actualizarCamposProducto(index) {
            const selectElement = document.getElementById(`producto_${index}`);
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const productoId = selectedOption.value;

            const nombreInput = document.getElementById(`nombre_producto_${index}`);
            const unidadSelect = document.getElementById(`unidad_medida_${index}`);
            const tipoSelect = document.getElementById(`tipo_producto_${index}`);

            if (productoId) {
                // Obtener los datos del producto seleccionado
                const producto = productosData.find(p => p.id_producto == productoId);

                if (producto) {
                    // Actualizar campos (aunque estén disabled, seteamos el valor)
                    if (nombreInput) {
                        nombreInput.value = producto.nombre || '';
                    }

                    if (unidadSelect && producto.medida) {
                        unidadSelect.value = producto.medida;
                    }

                    if (tipoSelect && producto.id_tipo) {
                        tipoSelect.value = producto.id_tipo;
                    }

                    // Guardar el tipo de producto en el grupo
                    const productGroup = document.querySelector(`.product-fields-group[data-product-index="${index}"]`);
                    if (productGroup) {
                        productGroup.dataset.productType = 'existing';
                        productGroup.dataset.productId = productoId;
                    }
                }
            } else {
                // Si se selecciona "Seleccionar producto existente", limpiar campos
                if (nombreInput) nombreInput.value = '';
                if (unidadSelect) unidadSelect.value = '';
                if (tipoSelect) tipoSelect.value = '';

                const productGroup = document.querySelector(`.product-fields-group[data-product-index="${index}"]`);
                if (productGroup) {
                    productGroup.dataset.productType = '';
                    productGroup.dataset.productId = '';
                }
            }
        }

        // Funcionalidad para añadir/duplicar campos de producto
        document.getElementById('addProductBtn').addEventListener('click', function () {
            addProductFields();
        });

        function addProductFields() {
            const container = document.getElementById('productFieldsContainer');
            const productGroups = container.querySelectorAll('.product-fields-group');
            const nextIndex = productGroups.length;

            // Limitar a 10 productos máximo
            if (nextIndex >= 10) {
                alert('Máximo 10 productos por solicitud');
                return;
            }

            // Obtener el HTML del primer grupo
            const firstGroup = productGroups[0];

            // Clonar el grupo
            const newGroup = firstGroup.cloneNode(true);

            // Actualizar el índice
            newGroup.setAttribute('data-product-index', nextIndex);

            // Actualizar título
            const title = newGroup.querySelector('.product-group-title');
            if (title) {
                title.textContent = `Producto #${nextIndex + 1}`;
            }

            // Actualizar atributo data-index en los botones de tipo
            const typeButtons = newGroup.querySelector('.product-type-buttons');
            if (typeButtons) {
                typeButtons.setAttribute('data-index', nextIndex);

                // Actualizar eventos onclick en los botones
                const newBtn = typeButtons.querySelector('[data-type="new"]');
                const existingBtn = typeButtons.querySelector('[data-type="existing"]');

                if (newBtn) {
                    newBtn.setAttribute('onclick', `seleccionarTipoProducto(${nextIndex}, 'new')`);
                }

                if (existingBtn) {
                    existingBtn.setAttribute('onclick', `seleccionarTipoProducto(${nextIndex}, 'existing')`);
                }

                // Resetear botones a estado inicial
                newBtn.classList.add('active');
                existingBtn.classList.remove('active');
            }

            // Actualizar todos los IDs y nombres
            const fieldsToUpdate = [
                { prefix: 'producto_', name: 'producto_id[]' },
                { prefix: 'nombre_producto_', name: 'nombre_producto[]' },
                { prefix: 'unidad_medida_', name: 'unidad_medida[]' },
                { prefix: 'cantidad_', name: 'cantidad[]' },
                { prefix: 'tipo_producto_', name: 'tipo_producto[]' }
            ];

            fieldsToUpdate.forEach(field => {
                // Actualizar input/select
                const input = newGroup.querySelector(`[id^="${field.prefix}"]`);
                if (input) {
                    const oldId = input.id;
                    const newId = `${field.prefix}${nextIndex}`;
                    input.id = newId;
                    input.name = field.name;

                    // Limpiar valor y resetear estado
                    if (input.type === 'text' || input.type === 'number') {
                        input.value = '';
                        input.disabled = false;
                    } else if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                        input.disabled = false;
                    }

                    // Actualizar el evento onchange para el select de productos
                    if (field.prefix === 'producto_') {
                        input.setAttribute('onchange', `actualizarCamposProducto(${nextIndex})`);
                        input.setAttribute('data-index', nextIndex);
                        input.disabled = true; // Deshabilitar por defecto
                    }

                    // Actualizar el for del label
                    const label = newGroup.querySelector(`label[for="${oldId}"]`);
                    if (label) {
                        label.htmlFor = newId;
                    }
                }
            });

            // Actualizar el ID del grupo de producto existente
            const existingProductGroup = newGroup.querySelector('.existing-product-field');
            if (existingProductGroup) {
                existingProductGroup.id = `existingProductGroup_${nextIndex}`;
                existingProductGroup.style.display = 'none'; // Ocultar por defecto
            }

            // AGREGAR BOTÓN DE ELIMINAR (esto es lo que faltaba)
            // Crear o actualizar el botón de eliminar
            let removeBtn = newGroup.querySelector('.remove-product-btn');
            if (!removeBtn) {
                removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-product-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i> Eliminar';
                removeBtn.setAttribute('aria-label', 'Eliminar producto');
                // Agregar al final del grupo
                newGroup.appendChild(removeBtn);
            }

            // Mostrar botón de eliminar
            removeBtn.style.display = 'block';
            removeBtn.onclick = function () {
                removeProductFields(newGroup);
            };

            // Agregar al contenedor
            container.appendChild(newGroup);

            // Actualizar contador
            updateProductsCounter();

            // Hacer scroll al nuevo producto añadido
            newGroup.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function removeProductFields(productGroup) {
            const container = document.getElementById('productFieldsContainer');
            if (container.children.length > 1) {
                productGroup.remove();
                updateProductIndexes();
                updateProductsCounter();
            }
        }

        function updateProductIndexes() {
            const productGroups = document.querySelectorAll('.product-fields-group');
            productGroups.forEach((group, index) => {
                group.setAttribute('data-product-index', index);

                // Actualizar título
                const title = group.querySelector('.product-group-title');
                if (title) {
                    title.textContent = `Producto #${index + 1}`;
                }

                // Actualizar todos los IDs y nombres
                const fieldsToUpdate = [
                    { prefix: 'producto_', name: 'producto_id[]' },
                    { prefix: 'nombre_producto_', name: 'nombre_producto[]' },
                    { prefix: 'unidad_medida_', name: 'unidad_medida[]' },
                    { prefix: 'cantidad_', name: 'cantidad[]' },
                    { prefix: 'tipo_producto_', name: 'tipo_producto[]' }
                ];

                fieldsToUpdate.forEach(field => {
                    // Actualizar input/select
                    const input = group.querySelector(`[id^="${field.prefix}"]`);
                    if (input) {
                        const newId = `${field.prefix}${index}`;
                        input.id = newId;
                        input.name = field.name;

                        // Actualizar el evento onchange para el select de productos
                        if (field.prefix === 'producto_') {
                            input.setAttribute('onchange', `actualizarCamposProducto(${index})`);
                            input.setAttribute('data-index', index);
                        }
                    }

                    // Actualizar label
                    const label = group.querySelector(`label[for^="${field.prefix}"]`);
                    if (label) {
                        const newFor = `${field.prefix}${index}`;
                        label.htmlFor = newFor;
                    }
                });

                // Actualizar botones de tipo
                const typeButtons = group.querySelector('.product-type-buttons');
                if (typeButtons) {
                    typeButtons.setAttribute('data-index', index);

                    const newBtn = typeButtons.querySelector('[data-type="new"]');
                    const existingBtn = typeButtons.querySelector('[data-type="existing"]');

                    if (newBtn) {
                        newBtn.setAttribute('onclick', `seleccionarTipoProducto(${index}, 'new')`);
                    }
                    if (existingBtn) {
                        existingBtn.setAttribute('onclick', `seleccionarTipoProducto(${index}, 'existing')`);
                    }
                }

                // Configurar botón de eliminar
                let removeBtn = group.querySelector('.remove-product-btn');
                if (!removeBtn) {
                    removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-product-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i> Eliminar';
                    removeBtn.setAttribute('aria-label', 'Eliminar producto');
                    group.appendChild(removeBtn);
                }

                // Mostrar u ocultar botón de eliminar
                if (index === 0) {
                    // Primer producto - ocultar botón de eliminar
                    removeBtn.style.display = 'none';
                } else {
                    // Productos adicionales - mostrar botón de eliminar
                    removeBtn.style.display = 'block';
                    removeBtn.onclick = function () {
                        removeProductFields(group);
                    };
                }
            });
        }

        function updateProductsCounter() {
            const count = document.querySelectorAll('.product-fields-group').length;
            const addBtn = document.getElementById('addProductBtn');

            // Deshabilitar botón cuando se alcance el límite
            if (count >= 10) {
                if (addBtn) {
                    addBtn.disabled = true;
                    addBtn.style.opacity = '0.6';
                    addBtn.style.cursor = 'not-allowed';
                }
            } else {
                if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.style.opacity = '1';
                    addBtn.style.cursor = 'pointer';
                }
            }

            // También puedes mostrar el contador si quieres
            const counterDisplay = document.getElementById('productCounter');
            if (!counterDisplay) {
                // Crear display del contador si no existe
                const counter = document.createElement('div');
                counter.id = 'productCounter';
                counter.className = 'product-counter';
                counter.innerHTML = `Productos: <strong>${count}</strong>/10`;

                // Insertar después del botón "Añadir otro producto"
                const addBtnContainer = document.querySelector('.add-product-btn').parentNode;
                addBtnContainer.appendChild(counter);
            } else {
                counterDisplay.innerHTML = `Productos: <strong>${count}</strong>/10`;
            }
        }

        // Función para aprobar una solicitud
        function aprobarSolicitud(idSolicitud) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Si la apruebas, será enviada al departamento de Presupuesto. ¿Deseas continuar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    cambiarEstadoSolicitud(idSolicitud, 'En Revisión');
                }
            });
        }

        // Función para rechazar una solicitud
        function rechazarSolicitud(idSolicitud) {
            Swal.fire({
                title: 'Rechazar Solicitud',
                text: 'Ingresa el motivo del rechazo (opcional):',
                input: 'text',
                inputPlaceholder: 'Motivo del rechazo...',
                inputAttributes: {
                    maxlength: 200
                },
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,

            }).then((result) => {
                if (result.isConfirmed) {
                    cambiarEstadoSolicitud(idSolicitud, 'Rechazado', result.value);
                }
            });
        }

        // Función principal para cambiar el estado de la solicitud
        function cambiarEstadoSolicitud(idSolicitud, nuevoEstado, motivo = '') {
            // Si el nuevo estado es "Aprobado" y el usuario es rol 4, confirmar
            if (nuevoEstado === 'Aprobado' && (userRol == 4 || userRol == 1)) {
                Swal.fire({
                    title: '¿Aprobar solicitud?',
                    text: 'Si la apruebas, la solicitud quedará finalizada. ¿Deseas continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        procederCambioEstado(idSolicitud, nuevoEstado, motivo);
                    }
                });
                return;
            }

            // Si el nuevo estado es "En Revisión" y el usuario es rol 3, confirmar
            if (nuevoEstado === 'En Revisión' && userRol == 3) {
                Swal.fire({
                    title: '¿Enviar a revisión?',
                    text: 'Si la envías a revisión, será evaluada por el departamento de Presupuesto. ¿Deseas continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        procederCambioEstado(idSolicitud, nuevoEstado, motivo);
                    }
                });
                return;
            }

            // Para rechazo u otros estados
            procederCambioEstado(idSolicitud, nuevoEstado, motivo);
        }

        // Función auxiliar para realizar el cambio de estado
        function procederCambioEstado(idSolicitud, nuevoEstado, motivo = '') {
            // Mostrar loading
            const swalLoading = Swal.fire({
                title: 'Procesando...',
                text: 'Actualizando estado de la solicitud',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Datos a enviar al servidor
            const datos = {
                id_solicitud: idSolicitud,
                nuevo_estado: nuevoEstado,
                motivo: motivo
            };

            // Realizar petición AJAX
            $.ajax({
                url: '?action=solicitudes&method=cambiarEstado',
                type: 'POST',
                data: datos,
                dataType: 'json',
                success: function (response) {
                    swalLoading.close();

                    if (response.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.message || 'Estado actualizado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Hubo un error al actualizar el estado',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    swalLoading.close();
                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor. Intenta nuevamente.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Aceptar'
                    });
                    console.error('Error AJAX:', error);
                }
            });
        }

        // Función para ver detalles de la solicitud
        function verDetallesSolicitud(solicitudes, idSolicitud) {
            // Mostrar loading
            const solicitud_seleccionada = solicitudes.find(s => s.id_solicitud == idSolicitud);
            const swalLoading = Swal.fire({
                title: 'Cargando...',
                text: 'Obteniendo detalles de la solicitud',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Datos a enviar al servidor
            const datos = {
                id_solicitud: idSolicitud,
                solicitud_seleccionada: JSON.stringify(solicitud_seleccionada)
            };

            // Realizar petición AJAX
            $.ajax({
                url: '?action=solicitudes&method=obtenerDetalles',
                type: 'POST',
                data: datos,
                dataType: 'json',
                success: function (response) {
                    swalLoading.close();

                    if (response.success && response.data) {
                        const solicitud = response.data;

                        // Crear contenido HTML para el modal
                        let contenidoHTML = `
                        <div class="solicitud-detalles" style="text-align: left; max-height: 400px; overflow-y: auto;">
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Solicitante:</strong> ${solicitud.nombre_solicitante || 'N/A'}
                            </div>
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Oficina destino:</strong> ${solicitud.nombre_oficina || 'N/A'}
                            </div>
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Fecha deseada:</strong> ${solicitud.fecha_deseo || 'N/A'}
                            </div>
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Estado:</strong> <span class="status-badge status-${(solicitud.estado || '').toLowerCase()}" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; display: inline-block;">${solicitud.estado || 'N/A'}</span>
                            </div>
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Fecha de creación:</strong> ${solicitud.fecha_solic.split(' ')[0] || 'N/A'}
                            </div>
                    `;
                        // Mostrar productos si existen
                        if (solicitud.productos && solicitud.productos.length > 0) {
                            contenidoHTML += `
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Productos solicitados (${solicitud.productos.length}):</strong>
                                <ul class="productos-lista" style="margin: 10px 0 0 20px; padding: 0;">
                        `;

                            solicitud.productos.forEach((producto, index) => {
                                contenidoHTML += `
                                <li style="margin-bottom: 5px; padding: 5px; background-color: #f9f9f9; border-radius: 4px;">
                                    <strong>${index + 1}.</strong> ${producto.nombre || 'Producto'} - 
                                    Cantidad: ${producto.un_deseadas || 0} 
                                    ${producto.medida || ''}.
                                    Tipo: ${producto.nombre_tipo ? ` ${producto.nombre_tipo}` : ''}
                                </li>
                            `;
                            });

                            contenidoHTML += `
                                </ul>
                            </div>
                        `;
                        }

                        // Mostrar notas si existen
                        if (solicitud.comentarios) {
                            contenidoHTML += `
                            <div class="detalle-item" style="margin-bottom: 15px;">
                                <strong>Notas:</strong>
                                <p style="background-color: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px; white-space: pre-wrap;">${solicitud.comentarios}</p>
                            </div>
                        `;
                        }

                        contenidoHTML += `</div>`;

                        // Mostrar modal con detalles
                        Swal.fire({
                            title: `Detalles de Solicitud #${idSolicitud}`,
                            html: contenidoHTML,
                            width: '600px',
                            showCloseButton: true,
                            confirmButtonText: 'Cerrar',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'No se pudieron obtener los detalles',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    swalLoading.close();
                    console.log("Error en la petición:", error);
                    console.log("Respuesta del servidor:", xhr.responseText);

                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'No se pudieron cargar los detalles de la solicitud',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
        // Función para recopilar productos del formulario
        function recopilarProductosDelFormulario() {
            const productGroups = document.querySelectorAll('.product-fields-group');
            const productos = [];

            productGroups.forEach((group, index) => {
                const tipo = group.querySelector('.product-type-buttons .active')?.dataset.type;
                const producto = { tipo: tipo };

                if (tipo === 'existing') {
                    const productoSelect = document.getElementById(`producto_${index}`);
                    if (productoSelect) {
                        producto.id_producto = productoSelect.value;

                        // Obtener datos del option seleccionado
                        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
                        if (selectedOption) {
                            producto.nombre = selectedOption.getAttribute('data-nombre') || '';
                            producto.medida = selectedOption.getAttribute('data-unidad') || '';
                            producto.id_tipo = selectedOption.getAttribute('data-tipo') || '';
                        }
                    }
                } else {
                    const nombreInput = document.getElementById(`nombre_producto_${index}`);
                    const unidadSelect = document.getElementById(`unidad_medida_${index}`);
                    const tipoSelect = document.getElementById(`tipo_producto_${index}`);

                    if (nombreInput) producto.nombre = nombreInput.value.trim();
                    if (unidadSelect) producto.medida = unidadSelect.value;
                    if (tipoSelect) producto.id_tipo = tipoSelect.value;
                }

                const cantidadInput = document.getElementById(`cantidad_${index}`);
                if (cantidadInput) producto.cantidad = cantidadInput.value;

                productos.push(producto);
            });

            return productos;
        }

        // Función para buscar productos similares
        function buscarProductosSimilares(nombreProducto, productosData, umbral = 0.8) {
            const productosSimilares = [];

            productosData.forEach(producto => {
                const similitud = calcularSimilitud(nombreProducto, producto.nombre);

                if (similitud >= umbral) {
                    productosSimilares.push({
                        producto: producto,
                        similitud: similitud,
                        id_producto: producto.id_producto,
                        nombre: producto.nombre,
                        medida: producto.medida,
                        tipo: producto.id_tipo
                    });
                }
            });

            // Ordenar por similitud descendente
            return productosSimilares.sort((a, b) => b.similitud - a.similitud);
        }

        // Función de similitud de texto
        function calcularSimilitud(str1, str2) {
            if (!str1 || !str2) return 0;

            str1 = str1.toLowerCase().trim();
            str2 = str2.toLowerCase().trim();

            if (str1 === str2) return 1;

            // Implementación simple
            const longer = str1.length > str2.length ? str1 : str2;
            const shorter = str1.length > str2.length ? str2 : str1;

            // Si la diferencia de longitud es muy grande, similitud baja
            if (longer.length === 0) return 1.0;

            // Coincidencia simple de palabras
            const words1 = str1.split(/\s+/);
            const words2 = str2.split(/\s+/);

            let matchingWords = 0;
            words1.forEach(word1 => {
                words2.forEach(word2 => {
                    if (word1 === word2) matchingWords++;
                });
            });

            const wordSimilarity = matchingWords / Math.max(words1.length, words2.length);

            // Coincidencia de caracteres
            let matchingChars = 0;
            for (let i = 0; i < Math.min(str1.length, str2.length); i++) {
                if (str1[i] === str2[i]) matchingChars++;
            }

            const charSimilarity = matchingChars / Math.max(str1.length, str2.length);

            // Promedio ponderado
            return (wordSimilarity * 0.7) + (charSimilarity * 0.3);
        }
        function buscarProductosSimilares(nombreProducto, productosData, umbral = 0.8) {
            const productosSimilares = [];

            productosData.forEach(producto => {
                const similitud = calcularSimilitud(nombreProducto, producto.nombre);

                if (similitud >= umbral) {
                    productosSimilares.push({
                        producto: producto,
                        similitud: similitud,
                        id_producto: producto.id_producto,
                        nombre: producto.nombre,
                        medida: producto.medida,
                        tipo: producto.id_tipo
                    });
                }
            });

            // Ordenar por similitud descendente
            return productosSimilares.sort((a, b) => b.similitud - a.similitud);
        }
        async function verificarDuplicadosAntesDeGuardar(productos) {
            const duplicadosEncontrados = [];

            for (let i = 0; i < productos.length; i++) {
                const producto = productos[i];

                if (producto.tipo === 'new' && producto.nombre) {
                    // Buscar productos similares en la base de datos
                    const similares = buscarProductosSimilares(producto.nombre, productosData, 0.7);

                    if (similares.length > 0) {
                        duplicadosEncontrados.push({
                            index: i,
                            nombreIngresado: producto.nombre,
                            similares: similares.slice(0, 3) // Mostrar solo los 3 más similares
                        });
                    }
                }
            }

            return {
                tieneDuplicados: duplicadosEncontrados.length > 0,
                duplicados: duplicadosEncontrados
            };
        }
        async function guardarSolicitudConVerificacion(event) {
            event.preventDefault();

            const form = document.getElementById('requestForm');
            const formMode = document.getElementById('formMode').value;
            const requestId = document.getElementById('requestId').value;

            const idSolicitanteField = document.getElementById('idSolicitante');
            if (!idSolicitanteField.value) {
                idSolicitanteField.value = userId;
            }
            console.log('id_solicitante:', idSolicitanteField.value);
            // 1. Validar formulario básico
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }

            // 2. Validar productos individualmente
            const productGroups = document.querySelectorAll('.product-fields-group');
            let productosValidos = true;
            let mensajeError = '';

            productGroups.forEach((group, index) => {
                const tipo = group.querySelector('.product-type-buttons .active')?.dataset.type;
                const productoSelect = document.getElementById(`producto_${index}`);
                const nombreInput = document.getElementById(`nombre_producto_${index}`);
                const cantidadInput = document.getElementById(`cantidad_${index}`);

                // Validar cantidad
                if (!cantidadInput || !cantidadInput.value || parseFloat(cantidadInput.value) <= 0) {
                    productosValidos = false;
                    mensajeError = `Debe ingresar una cantidad válida para el Producto #${index + 1}`;
                    if (cantidadInput) cantidadInput.style.borderColor = 'red';
                    return;
                }

                if (tipo === 'existing') {
                    // Validar que se haya seleccionado un producto
                    if (!productoSelect || !productoSelect.value) {
                        productosValidos = false;
                        mensajeError = `Debe seleccionar un producto para el Producto #${index + 1}`;
                        if (productoSelect) productoSelect.style.borderColor = 'red';
                    }
                } else {
                    // Validar que se haya ingresado un nombre
                    if (!nombreInput || !nombreInput.value.trim()) {
                        productosValidos = false;
                        mensajeError = `Debe ingresar un nombre para el Producto #${index + 1}`;
                        if (nombreInput) nombreInput.style.borderColor = 'red';
                    }
                }
            });

            if (!productosValidos) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en productos',
                    text: mensajeError
                });
                return false;
            }

            // 3. Recopilar productos para verificación
            const productos = recopilarProductosDelFormulario();

            // 4. Verificar duplicados solo para productos nuevos
            const verificacion = await verificarDuplicadosAntesDeGuardar(productos);

            if (verificacion.tieneDuplicados) {
                // Mostrar modal de confirmación con sugerencias
                const usarExistentes = await mostrarModalDuplicados(verificacion.duplicados);

                if (usarExistentes) {
                    // Actualizar el formulario con productos existentes seleccionados
                    await actualizarFormularioConExistentes(verificacion.duplicados);

                    // Dar tiempo para que se actualicen los campos
                    await new Promise(resolve => setTimeout(resolve, 100));

                    // Volver a recopilar productos después de actualizar
                    productos = recopilarProductosDelFormulario();
                }
                // Si el usuario elige "Mantener nuevos", no hacemos cambios
            }

            // 5. Determinar cómo proceder según el modo
            if (formMode === 'edit') {
                // Modo edición
                return await guardarEdicion(requestId, productos);
            } else {
                // Modo agregar - usar AJAX que controlamos
                return await guardarNuevaSolicitudAjax(form);
            }
        }
        // Nueva función para AJAX controlado
        async function guardarNuevaSolicitudAjax(form) {
            closeModal();
            // Crear FormData manual
            const formData = new FormData();

            // Asegurar id_solicitante
            const idSolicitante = document.getElementById('idSolicitante').value || userId;
            formData.append('id_solicitante', idSolicitante);

            // Agregar otros campos necesarios
            formData.append('departamento', document.getElementById('departamento').value);
            formData.append('fecha_requerida', document.getElementById('fecha_requerida').value);
            formData.append('notas', document.getElementById('notas').value);
            formData.append('form_mode', 'add');

            // Agregar productos
            const productos = recopilarProductosDelFormulario();
            const productosParaEnviar = prepararProductosParaEnvio(productos);

            productosParaEnviar.forEach((prod, index) => {
                formData.append(`producto_id[]`, prod.id_producto || '');
                formData.append(`nombre_producto[]`, prod.nombre_producto || '');
                formData.append(`unidad_medida[]`, prod.unidad_medida || '');
                formData.append(`cantidad[]`, prod.cantidad || '');
                formData.append(`tipo_producto[]`, prod.tipo_producto || '');
            });

            try {
                const response = await fetch('?action=solicitudes&method=home', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Solicitud creada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    location.reload();
                    return true;
                } else {
                    throw new Error('Error en el servidor');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo crear la solicitud'
                });
                return false;
            }
        }
        function prepararYEnviarFormularioTradicional(form) {
            // 1. Asegurar que el campo hidden tenga valor ANTES de crear FormData
            const idSolicitanteField = document.getElementById('idSolicitante');
            if (!idSolicitanteField.value) {
                idSolicitanteField.value = userId;
                console.log('idSolicitante establecido a:', userId);
            }

            // 2. Debug: Verificar todos los campos
            console.log('=== DEBUG: Campos del formulario ===');
            const formElements = form.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (element.name) {
                    console.log(`${element.name}: ${element.value} (type: ${element.type})`);
                }
            }
            console.log('idSolicitante:', idSolicitanteField.value);

            // 3. Habilitar campos deshabilitados
            const disabledFields = form.querySelectorAll('input:disabled, select:disabled');
            disabledFields.forEach(field => {
                field.disabled = false;
            });

            // 4. Crear FormData MANUALMENTE incluyendo todos los campos necesarios
            const formData = new FormData();

            // Agregar todos los campos del formulario
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (element.name && !element.disabled) {
                    if (element.type === 'checkbox' || element.type === 'radio') {
                        if (element.checked) {
                            formData.append(element.name, element.value);
                        }
                    } else if (element.type === 'file') {
                        // Para archivos
                        if (element.files.length > 0) {
                            formData.append(element.name, element.files[0]);
                        }
                    } else {
                        // Para todos los demás (text, number, hidden, select, etc.)
                        if (element.value || element.value === '0') {
                            formData.append(element.name, element.value);
                        }
                    }
                }
            }

            // 5. Asegurar específicamente id_solicitante
            if (!formData.has('id_solicitante') && idSolicitanteField.value) {
                formData.append('id_solicitante', idSolicitanteField.value);
            }

            // 6. Debug del FormData
            console.log('=== DEBUG: FormData a enviar ===');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }

            // 7. Mostrar loading
            Swal.fire({
                title: 'Guardando solicitud...',
                text: 'Por favor, espere un momento',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // 8. Enviar con fetch para tener más control
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    Swal.close();
                    if (response.ok) {
                        // Éxito - recargar página
                        location.reload();
                    } else {
                        // Error
                        return response.text().then(text => {
                            throw new Error(`Error del servidor: ${response.status}`);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar la solicitud. Intente nuevamente.'
                    });
                });

            return false; // Prevenir envío tradicional
        }
        function actualizarProductosConExistentes(productos, duplicados) {
            // Crear una copia de los productos
            const productosActualizados = [...productos];

            duplicados.forEach(duplicado => {
                const radios = document.querySelectorAll(`input[name="sugerencia_${duplicado.index}"]:checked`);
                if (radios.length > 0) {
                    const idProductoSeleccionado = radios[0].value;
                    const sugerenciaSeleccionada = duplicado.similares.find(s => s.id_producto == idProductoSeleccionado);

                    if (sugerenciaSeleccionada) {
                        // Actualizar el producto en el array
                        productosActualizados[duplicado.index] = {
                            tipo: 'existing',
                            id_producto: sugerenciaSeleccionada.id_producto,
                            nombre: sugerenciaSeleccionada.nombre,
                            medida: sugerenciaSeleccionada.medida,
                            id_tipo: sugerenciaSeleccionada.tipo,
                            cantidad: productos[duplicado.index].cantidad
                        };
                    }
                }
            });

            return productosActualizados;
        }
        async function actualizarFormularioConExistentes(duplicados) {
            // Para cada duplicado, cambiar el tipo a "existing" y seleccionar el producto
            duplicados.forEach(duplicado => {
                const radios = document.querySelectorAll(`input[name="sugerencia_${duplicado.index}"]:checked`);
                if (radios.length > 0) {
                    const idProductoSeleccionado = radios[0].value;

                    // Obtener el grupo del producto
                    const group = document.querySelector(`.product-fields-group[data-product-index="${duplicado.index}"]`);
                    if (!group) return;

                    // Cambiar a tipo "existing"
                    const buttons = group.querySelector('.product-type-buttons');
                    const existingBtn = buttons.querySelector('[data-type="existing"]');
                    const newBtn = buttons.querySelector('[data-type="new"]');

                    newBtn.classList.remove('active');
                    existingBtn.classList.add('active');

                    // Actualizar el select de productos
                    const productoSelect = document.getElementById(`producto_${duplicado.index}`);
                    if (productoSelect) {
                        // Buscar si la opción ya existe
                        let optionExists = false;
                        for (let option of productoSelect.options) {
                            if (option.value === idProductoSeleccionado) {
                                optionExists = true;
                                break;
                            }
                        }

                        // Si no existe, agregarla
                        if (!optionExists) {
                            const similar = duplicado.similares.find(s => s.id_producto == idProductoSeleccionado);
                            if (similar) {
                                const option = document.createElement('option');
                                option.value = similar.id_producto;
                                option.textContent = `${similar.nombre} (Inactivo)`;
                                option.setAttribute('data-nombre', similar.nombre || '');
                                option.setAttribute('data-unidad', similar.medida || '');
                                option.setAttribute('data-tipo', similar.tipo || '');
                                productoSelect.appendChild(option);
                            }
                        }

                        // Seleccionar el producto
                        productoSelect.value = idProductoSeleccionado;
                        productoSelect.disabled = false;

                        // Actualizar campos automáticamente
                        actualizarCamposProducto(duplicado.index);
                    }
                }
            });
        }
        async function mostrarModalDuplicados(duplicados) {
            return new Promise((resolve) => {
                let html = `
            <div class="duplicados-modal" style="text-align: left; max-height: 400px; overflow-y: auto;">
                <div class="duplicados-header" style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <i data-lucide="alert-triangle" style="color: #f59e0b; width: 24px; height: 24px;"></i>
                    <h3 style="margin: 0; color: #333;">Productos similares encontrados</h3>
                </div>
                <div class="duplicados-content">
                    <p>Se encontraron productos similares en la base de datos. ¿Desea usar los productos existentes?</p>
        `;

                duplicados.forEach(duplicado => {
                    html += `
                <div class="duplicado-item" style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 15px; border-left: 4px solid #f59e0b;">
                    <div class="duplicado-info" style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #dee2e6;">
                        <strong>Producto #${duplicado.index + 1}:</strong> "${duplicado.nombreIngresado}"
                    </div>
                    <div class="sugerencias" style="background: white; border-radius: 6px; padding: 12px; border: 1px solid #e9ecef;">
                        <p style="margin-top: 0; margin-bottom: 10px; font-size: 0.9em; color: #666;"><small>Productos similares encontrados:</small></p>
            `;

                    duplicado.similares.forEach((similar, idx) => {
                        html += `
                    <div class="sugerencia-item" style="margin-bottom: 8px; padding: 8px; border-radius: 4px; transition: background-color 0.2s;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; width: 100%;">
                            <input type="radio" 
                                   name="sugerencia_${duplicado.index}" 
                                   value="${similar.id_producto}"
                                   ${idx === 0 ? 'checked' : ''}
                                   style="margin-right: 5px;">
                            ${similar.nombre} (${similar.medida})
                            <span class="similitud" style="margin-left: auto; font-size: 0.85em; color: #6c757d; background: #e9ecef; padding: 2px 8px; border-radius: 12px;">
                                ${Math.round(similar.similitud * 100)}% similar
                            </span>
                        </label>
                    </div>
                `;
                    });

                    html += `
                    </div>
                </div>
            `;
                });

                html += `
                </div>
            </div>
        `;

                Swal.fire({
                    title: '¿Duplicados encontrados?',
                    html: html,
                    width: 600,
                    showCancelButton: true,
                    confirmButtonText: 'Usar productos existentes',
                    cancelButtonText: 'Mantener como nuevos',
                    reverseButtons: true,
                    customClass: {
                        container: 'swal2-high-zindex'
                    },
                    didOpen: () => {
                        // Asegurar que SweetAlert esté por encima de tu modal
                        const container = document.querySelector('.swal2-container');
                        if (container) {
                            container.style.zIndex = '999999';
                        }
                        lucide.createIcons();
                    }
                }).then((result) => {
                    resolve(result.isConfirmed); // true si eligió usar existentes
                });
            });
        }
        // Agrega esto al final de tu CSS dinámico
        const removeButtonStyles = `
    .remove-product-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        z-index: 10;
    }
    
    .remove-product-btn:hover {
        background: #c82333;
        transform: scale(1.05);
    }
    
    .remove-product-btn:active {
        transform: scale(0.95);
    }
    
    .remove-product-btn i {
        font-size: 0.9rem;
    }
    
    /* Asegurar que el grupo de productos tenga posición relativa */
    .product-fields-group {
        position: relative;
        padding-top: 10px;
    }
    
    /* Ajustar el header para que no se superponga */
    .product-group-header {
        padding-right: 100px; /* Espacio para el botón */
    }
`;

        // Agregar el CSS al documento
        const styleElement = document.createElement('style');
        styleElement.textContent = removeButtonStyles;
        document.head.appendChild(styleElement);
    </script>
</body>

</html>