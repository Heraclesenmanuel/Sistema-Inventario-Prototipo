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
                    <?php if($_SESSION['dpto'] !=4  && $_SESSION['dpto'] != 3): ?>
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
                    <input type="text" id="searchInput" placeholder="Buscar por solicitante, oficina..." aria-label="Buscar solicitudes">
                </div>
                
                <div class="filter-group">
                    <label for="statusFilter" class="filter-label">Filtrar por estado:</label>
                    <?php if ($_SESSION['dpto'] != 4): ?>
                    <select id="statusFilter" class="filter-select" aria-label="Filtrar por estado">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="En Revisión">En Revisión</option>
                        <option value="Rechazado">Rechazado</option>
                        <option value="Aprobado">Rechazado</option>
                    </select>
                    <?php else: ?>
                        <select id="statusFilter" class="filter-select" aria-label="Filtrar por estado">
                        <option value="">Todos los estados</option>
                        <option value="En Revisión">Aprobado</option>
                        <option value="Aprobado">Rechazado</option>
                    </select>
                    <?php endif; ?>
                </div>
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
                        <?php 
                        $solicitudes = !empty($solicitudes) ? $solicitudes : [];
                        ?>
                        
                        <?php if(!empty($solicitudes)): ?>
                            <?php foreach($solicitudes as $solicitud): ?>
                                <tr data-status="<?= htmlspecialchars($solicitud['estado']) ?>" 
                                    data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                    class="request-row">
                                    <td>
                                        <strong><?= htmlspecialchars($solicitud['nombre_solicitante']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($solicitud['nombre_oficina']) ?></td>
                                    <td><?= htmlspecialchars($solicitud['fecha_deseo']) ?></td>

                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $solicitud['estado'])) ?>">
                                            <?php 
                                            // Icono según estado
                                            $iconName = 'clock';
                                            if($solicitud['estado'] === 'En Revisión') $iconName = 'check-circle';
                                            if($solicitud['estado'] === 'Rechazado') $iconName = 'x-circle';
                                            ?>
                                            <i data-lucide="<?= $iconName ?>" style="width:14px; height:14px;"></i>
                                            <?= htmlspecialchars($solicitud['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if($solicitud['estado'] === 'Pendiente'): ?>
                                                <button type="button" class="btn-action approve" 
                                                        onclick="aprobarSolicitud(<?= htmlspecialchars($solicitud['id_solicitud']) ?>)"
                                                        data-tippy-content="Aprobar">
                                                    <i data-lucide="check"></i>
                                                </button>
                                                <button type="button" class="btn-action reject" 
                                                        onclick="rechazarSolicitud(<?= htmlspecialchars($solicitud['id_solicitud']) ?>)"
                                                        data-tippy-content="Rechazar">
                                                    <i data-lucide="x"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn-action view" 
                                                    onclick='verDetallesSolicitud(<?= json_encode($solicitudes) ?>, <?= (int)$solicitud["id_solicitud"] ?>)'
                                                    data-tippy-content="Ver Detalles">
                                                <i data-lucide="eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div id="emptyState" class="empty-state" style="display: <?= empty($solicitudes) ? 'flex' : 'none' ?>;">
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
        
        <form id="requestForm" name="requestForm" method="POST" action="?action=solicitudes&method=home" novalidate onsubmit="return guardarSolicitud(event)">
            <input type="hidden" id="requestId" name="request_id">
            <input type="hidden" id="formMode" name="form_mode" value="add">
            
            <div class="modal-body">
                <div class="form-grid">
                    <?php
                    $departamento = $_SESSION['dpto'];
                    ?>
                    <div class="form-group">
                        <label for="departamento" class="required">Departamento de destino</label>
                        <select id="departamento" name="departamento" class="form-select" required>
                            <?php if(isset($oficinas['success']) && $oficinas['success'] && !empty($oficinas['data'])): ?>
                                <?php foreach($oficinas['data'] as $oficina): ?>
                                    <option value="<?php echo $oficina['num_oficina']?>" <?php echo $departamento==$oficina['num_oficina'] ? "selected" : "" ?>><?php echo $oficina['nombre']?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="313" <?= $departamento == "Biblioteca" ? "selected" : "" ?>>Biblioteca</option>
                                <option value="212" <?= $departamento == "Informatica" ? "selected" : "" ?>>Informatica</option>
                                <option value="143" <?= $departamento == "Cuentas" ? "selected" : "" ?>>Cuentas</option>
                                <option value="204" <?= $departamento == "Deportes" ? "selected" : "" ?>>Deportes</option>
                                <option value="305" <?= $departamento == "Consejeria/Orientacion" ? "selected" : "" ?>>Consejeria/Orientacion</option>
                                <option value="205" <?= $departamento == "Servicios Generales" ? "selected" : "" ?>>Servicios Generales</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    
                    <!-- Contenedor para grupos de campos de productos -->
                    <div class="product-fields-container" id="productFieldsContainer">
                        <!-- Grupo inicial de campos de producto -->
                        <div class="product-fields-group" data-product-index="0">
                            <div class="product-group-title">Producto #1</div>
                            
<div class="product-fields-grid">
    <div class="form-group">
        <label for="producto_0">Producto existente</label>
        <select class="filter-select" name="producto_id[]" id="producto_0" data-index="0" onchange="actualizarCamposProducto(0)">
            <option value="">-- Seleccionar producto existente --</option>
            <?php foreach($productos['data'] as $producto): ?>
                <option value="<?php echo $producto['id_producto']?>" 
                        data-nombre="<?php echo htmlspecialchars($producto['nombre'])?>"
                        data-unidad="<?php echo htmlspecialchars($producto['unidad_medida'] ?? '')?>"
                        data-tipo="<?php echo $producto['id_tipo'] ?? ''?>">
                    <?php echo $producto['nombre']?>
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
            <?php if(isset($tipos_p['success']) && $tipos_p['success'] && !empty($tipos_p['data'])): ?>
                <?php foreach($tipos_p['data'] as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo']?>"><?php echo $tipo['nombre']?></option>
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
            
            // Establecer fecha mínima como hoy
            const today = new Date();
            document.getElementById('fecha_requerida').min = today.toISOString().split('T')[0];
            
            // Establecer fecha por defecto (7 días en el futuro)
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            document.getElementById('fecha_requerida').valueAsDate = nextWeek;
            
            // Inicializar contador de caracteres
            document.getElementById('notas').addEventListener('input', function() {
                document.getElementById('charCount').textContent = this.value.length;
            });
            
        } else if (mode === 'edit') {
            modalTitle.textContent = 'Editar Solicitud';
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
    document.getElementById('requestModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
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
            console.log(solicitudesData)
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

            const clone = template.cloneNode(true);
            const newIndex = currentCount; // Simple increment
            
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
                
                // Obtener referencias a los elementos
                const productoSelect = document.getElementById(`producto_${index}`);
                const nombreInput = document.getElementById(`nombre_producto_${index}`);
                const unidadSelect = document.getElementById(`unidad_medida_${index}`);
                const cantidadInput = document.getElementById(`cantidad_${index}`);
                const tipoSelect = document.getElementById(`tipo_producto_${index}`);
                
                // Llenar campos
                if (productoSelect && producto.id_producto) {
                    productoSelect.value = producto.id_producto;
                }
                
                if (nombreInput) {
                    nombreInput.value = producto.nombre || '';
                }
                
                if (unidadSelect && producto.medida) {
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
    
    // Función para guardar la solicitud
    function guardarSolicitud(event) {
        event.preventDefault();
        
        const form = document.getElementById('requestForm');
        const formMode = document.getElementById('formMode').value;
        
        // Validar formulario
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }
        
        // Mostrar loading
        const swalLoading = Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Si es modo editar, cambiar la acción
        if (formMode === 'edit') {
            const formData = new FormData(form);
            if(userRol == 4)
            {
                formData.append('nuevo_estado', 'Aprobado')
                formData.append('id_solicitud', formData.get('request_id'))
            }
            // Agregar productos al FormData
            const productGroups = document.querySelectorAll('.product-fields-group');
            productGroups.forEach((group, index) => {
                const productoId = document.getElementById(`producto_${index}`)?.value || '';
                const nombreProducto = document.getElementById(`nombre_producto_${index}`)?.value || '';
                const unidadMedida = document.getElementById(`unidad_medida_${index}`)?.value || '';
                const cantidad = document.getElementById(`cantidad_${index}`)?.value || '';
                const tipoProducto = document.getElementById(`tipo_producto_${index}`)?.value || '';
                
                // Los campos ya están en el FormData por ser arrays []
                // Solo nos aseguramos de que tengan valores
            });
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
                }
            fetch('?action=solicitudes&method=actualizarSolic', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                swalLoading.close();
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Solicitud actualizada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al actualizar la solicitud'
                    });
                }
            })
            .catch(error => {
                swalLoading.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la solicitud'
                });
            });
        } else {
            // Modo agregar - enviar formulario normalmente
            form.submit();
        }
        
        return false;
    }
    
    // Función para actualizar campos cuando se selecciona un producto existente
    function actualizarCamposProducto(index) {
        const selectElement = document.getElementById(`producto_${index}`);
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const productoId = selectedOption.value;
        
        if (productoId) {
            // Obtener los datos del producto seleccionado
            const producto = productosData.find(p => p.id_producto == productoId);
        
            if (producto) {
                // Actualizar campo de nombre
                const nombreInput = document.getElementById(`nombre_producto_${index}`);
                if (nombreInput) {
                    nombreInput.value = producto.nombre || '';
                }
                
                // Actualizar campo de unidad de medida
                const unidadSelect = document.getElementById(`unidad_medida_${index}`);
                if (unidadSelect && producto.medida) {
                    unidadSelect.value = producto.medida;
                }
                
                // Actualizar campo de tipo de producto
                const tipoSelect = document.getElementById(`tipo_producto_${index}`);
                if (tipoSelect && producto.id_tipo) {
                    tipoSelect.value = producto.id_tipo;
                }
            }
        }
    }
    
    // Funcionalidad para añadir/duplicar campos de producto
    document.getElementById('addProductBtn').addEventListener('click', function() {
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
                
                // Limpiar valor
                if (input.type === 'text' || input.type === 'number') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
                
                // Actualizar el evento onchange para el select de productos
                if (field.prefix === 'producto_') {
                    input.setAttribute('onchange', `actualizarCamposProducto(${nextIndex})`);
                    input.setAttribute('data-index', nextIndex);
                }
            }
            
            // Actualizar label
            const label = newGroup.querySelector(`label[for^="${field.prefix}"]`);
            if (label) {
                const oldFor = label.htmlFor;
                const newFor = `${field.prefix}${nextIndex}`;
                label.htmlFor = newFor;
            }
        });
        
        // Agregar botón de eliminar
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-product-btn';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.setAttribute('aria-label', 'Eliminar producto');
        removeBtn.addEventListener('click', function() {
            removeProductFields(this.parentElement);
        });
        newGroup.appendChild(removeBtn);
        
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
            
            // Agregar botón de eliminar a todos los grupos excepto el primero
            if (index > 0) {
                // Verificar si ya existe un botón de eliminar
                let removeBtn = group.querySelector('.remove-product-btn');
                if (!removeBtn) {
                    removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-product-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.setAttribute('aria-label', 'Eliminar producto');
                    removeBtn.addEventListener('click', function() {
                        removeProductFields(this.parentElement);
                    });
                    group.appendChild(removeBtn);
                }
            } else {
                // Remover botón de eliminar del primer grupo si existe
                const removeBtn = group.querySelector('.remove-product-btn');
                if (removeBtn) {
                    removeBtn.remove();
                }
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
    }

    // Inicializar contador al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        updateProductsCounter();
    });

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
            success: function(response) {
                swalLoading.close();
                
                if (response.success) {
                    // Mostrar éxito
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.message || 'Estado actualizado correctamente',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Recargar la página para ver los cambios
                        location.reload();
                    });
                } else {
                    // Mostrar error
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Hubo un error al actualizar el estado',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
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
            success: function(response) {
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
                                <strong>Fecha de creación:</strong> ${solicitud.fecha_solic || 'N/A'}
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
                                    ${producto.medida || ''}
                                    ${producto.nombre_tipo ? ` (${producto.nombre_tipo})` : ''}
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
            error: function(xhr, status, error) {
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
    
    // Agregar botón de editar en la tabla si es necesario
    document.addEventListener('DOMContentLoaded', function() {
        // Si necesitas agregar botones de editar dinámicamente
        const tableBody = document.getElementById('requestsTableBody');
        if (tableBody) {
            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('[data-action="edit"]')) {
                    const solicitudId = e.target.closest('[data-action="edit"]').getAttribute('data-id');
                    if (solicitudId) {
                        editarSolicitud(solicitudId);
                    }
                }
            });
        }
    });
</script>
</body>
</html>