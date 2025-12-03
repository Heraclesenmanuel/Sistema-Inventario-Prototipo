<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'APP' ?> - Solicitudes</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/solicitudes.css">
    
    <!-- Añade jQuery y SweetAlert2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
</head>
<body>
   <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <h1>Gestión de Solicitudes</h1>
                    <p class="subtitle">Administra y organiza las solicitudes de productos por departamento</p>
                </div>
                <button class="btn-primary" onclick="openModal()" id="newRequestBtn">
                    <i class="fas fa-plus"></i>
                    Nueva Solicitud
                </button>
            </div>

            <div class="search-filter-bar">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar solicitudes..." 
                           aria-label="Buscar solicitudes">
                </div>
                
                <div class="filter-group">
                    <label for="statusFilter" class="filter-label">Filtrar por estado:</label>
                    <select id="statusFilter" class="filter-select" aria-label="Filtrar por estado">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Revisión">Aprobado</option>
                        <option value="Rechazado">Rechazado</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="requests-table" id="requestsTable" aria-label="Lista de solicitudes">
                        <thead>
                            <tr>
                                <th scope="col">Solicitante</th>
                                <th scope="col">Oficina de Destino</th>
                                <th scope="col">Fecha deseada</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <?php 
                            // Datos de ejemplo para desarrollo
                            
                            $solicitudes = !empty($solicitudes) ? $solicitudes : [];
                            ?>
                            
                            <?php if($cant_solicts_no_en_rev > 0): ?>
                                <?php foreach($solicitudes as $solicitud): ?>
                                    <?php if($solicitud['estado'] !== 'En Revisión'): ?>
                                    <tr data-status="<?= htmlspecialchars($solicitud['estado']) ?>" 
                                        data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>">
                                        <td><?= htmlspecialchars($solicitud['nombre_solicitante']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['nombre_oficina']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['fecha_deseo']) ?></td>

                                        <td>
                                            <span class="status-badge status-<?= strtolower($solicitud['estado']) ?>">
                                                <?= htmlspecialchars($solicitud['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if($solicitud['estado'] === 'Pendiente'): ?>
                                                    <button type="button" class="btn-action approve" onclick="aprobarSolicitud(<?= htmlspecialchars($solicitud['id_solicitud']) ?>)"
                                                            data-action="approve" 
                                                            data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                                            aria-label="Aprobar solicitud">
                                                        <i class="fas fa-check"></i>
                                                        <span>Aprobar</span>
                                                    </button>
                                                    <button type="button" class="btn-action reject" 
                                                            onclick="rechazarSolicitud(<?= htmlspecialchars($solicitud['id_solicitud']) ?>)"
                                                            data-action="reject"
                                                            data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                                            aria-label="Rechazar solicitud">
                                                        <i class="fas fa-times"></i>
                                                        <span>Rechazar</span>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn-action view" 
                                                        onclick='verDetallesSolicitud(<?= json_encode($solicitudes) ?>, <?= (int)$solicitud["id_solicitud"] ?>)'
                                                        data-action="view"
                                                        data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                                        aria-label="Ver detalles de solicitud">
                                                    <i class="fas fa-eye"></i>
                                                    <span>Ver</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center no-data">No hay solicitudes disponibles</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div id="emptyState" class="empty-state" style="display: <?= empty($solicitudes) ? 'flex' : 'none' ?>;">
                    <div class="empty-state-content">
                        <i class="fas fa-inbox empty-icon"></i>
                        <h3>No hay solicitudes</h3>
                        <p>Comienza agregando tu primera solicitud</p>
                        <button class="btn-primary" onclick="openModal()">
                            <i class="fas fa-plus"></i>
                            Agregar Solicitud
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="loading-state" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p>Cargando solicitudes...</p>
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
            
            <form id="requestForm" name="requestForm" method="POST" action="?action=solicitudes&method=home" novalidate>
                <input type="hidden" id="requestId" name="request_id">
                <div class="modal-body">
                    <div class="form-grid">
                        <?php
                        $departamento = $_SESSION['num_oficina'];
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
        <select class="filter-select" name="producto_id[]" id="producto_0">
            <option value="">-- Seleccionar producto existente --</option>
            <?php foreach($productos['data'] as $producto): ?>
                <option value="<?php echo $producto['id_producto']?>"><?php echo $producto['nombre']?></option>;
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
        // Funciones básicas para el modal
        function openModal() {
            document.getElementById('requestModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            
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
        }
        
        function closeModal() {
            document.getElementById('requestModal').classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('requestForm').reset();
            
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
            
            // Crear nuevo grupo de campos
            const newProductGroup = document.createElement('div');
            newProductGroup.className = 'product-fields-group';
            newProductGroup.setAttribute('data-product-index', nextIndex);
            
            // Crear título del grupo
            const groupTitle = document.createElement('div');
            groupTitle.className = 'product-group-title';
            groupTitle.textContent = `Producto #${nextIndex + 1}`;
            newProductGroup.appendChild(groupTitle);
            
            // Crear botón de eliminar (solo para grupos adicionales)
            if (nextIndex > 0) {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-product-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.setAttribute('aria-label', 'Eliminar producto');
                removeBtn.addEventListener('click', function() {
                    removeProductFields(this.parentElement);
                });
                newProductGroup.appendChild(removeBtn);
            }
            
            // Clonar campos del primer grupo
            const firstGroup = productGroups[0];
            const fieldsToClone = [
                { 
                    selector: '.form-group:has(#producto_0)',
                    idSuffix: '_' + nextIndex,
                    name: 'producto_id[]'
                },
                { 
                    selector: '.form-group:has(#nombre_producto_0)',
                    idSuffix: '_' + nextIndex,
                    name: 'nombre_producto[]'
                },
                { 
                    selector: '.form-group:has(#unidad_medida_0)',
                    idSuffix: '_' + nextIndex,
                    name: 'unidad_medida[]'
                },
                { 
                    selector: '.form-group:has(#cantidad_0)',
                    idSuffix: '_' + nextIndex,
                    name: 'cantidad[]'
                },
                { 
                    selector: '.form-group:has(#tipo_producto_0)',
                    idSuffix: '_' + nextIndex,
                    name: 'tipo_producto[]'
                }
            ];
            
            // Crear grid para los campos
            const productGrid = document.createElement('div');
            productGrid.className = 'product-fields-grid';
            
            fieldsToClone.forEach(fieldConfig => {
                const originalElement = firstGroup.querySelector(fieldConfig.selector);
                if (originalElement) {
                    const clonedElement = originalElement.cloneNode(true);
                    
                    // Actualizar IDs y nombres
                    const inputs = clonedElement.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        // Actualizar ID
                        if (input.id) {
                            input.id = input.id.replace(/_0$/, fieldConfig.idSuffix);
                        }
                        
                        // Actualizar nombre
                        input.name = fieldConfig.name;
                        
                        // Limpiar valores
                        if (input.type === 'text' || input.type === 'number') {
                            input.value = '';
                        } else if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        }
                    });
                    
                    // Actualizar etiquetas
                    const labels = clonedElement.querySelectorAll('label');
                    labels.forEach(label => {
                        if (label.htmlFor) {
                            label.htmlFor = label.htmlFor.replace(/_0$/, fieldConfig.idSuffix);
                        }
                    });
                    
                    productGrid.appendChild(clonedElement);
                }
            });
            
            newProductGroup.appendChild(productGrid);
            container.appendChild(newProductGroup);
            updateProductIndexes();
            updateProductsCounter();
            
            // Hacer scroll al nuevo producto añadido
            newProductGroup.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        function removeProductFields(productGroup) {
            if (document.querySelectorAll('.product-fields-group').length > 1) {
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
                
                // Actualizar IDs y nombres de los campos
                const inputs = group.querySelectorAll('input, select');
                inputs.forEach(input => {
                    // Actualizar ID
                    if (input.id) {
                        const baseId = input.id.replace(/_\d+$/, '');
                        input.id = baseId + '_' + index;
                    }
                });
                
                // Actualizar etiquetas asociadas
                const labels = group.querySelectorAll('label');
                labels.forEach(label => {
                    if (label.htmlFor && (
                        label.htmlFor.startsWith('producto_') || 
                        label.htmlFor.startsWith('nombre_producto_') ||
                        label.htmlFor.startsWith('unidad_medida_') ||
                        label.htmlFor.startsWith('cantidad_') ||
                        label.htmlFor.startsWith('tipo_producto_')
                    )) {
                        const baseFor = label.htmlFor.replace(/_\d+$/, '');
                        label.htmlFor = baseFor + '_' + index;
                    }
                });
            });
        }
        
        function updateProductsCounter() {
            const count = document.querySelectorAll('.product-fields-group').length;
            const counter = document.getElementById('productsCount');
            const maxHint = document.getElementById('maxProductsHint');
            const addBtn = document.getElementById('addProductBtn');
            
            if (counter) {
                counter.textContent = `${count} producto${count !== 1 ? 's' : ''}`;
            }
            
            // Mostrar advertencia cuando se acerque al límite
            if (maxHint) {
                if (count >= 8) {
                    maxHint.style.display = 'block';
                } else {
                    maxHint.style.display = 'none';
                }
            }
            
            // Deshabilitar botón cuando se alcance el límite
            if (count >= 10) {
                addBtn.disabled = true;
                addBtn.style.opacity = '0.6';
                addBtn.style.cursor = 'not-allowed';
            } else {
                addBtn.disabled = false;
                addBtn.style.opacity = '1';
                addBtn.style.cursor = 'pointer';
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
                                    <strong>Productos solicitados:</strong>
                                    <ul class="productos-lista" style="margin: 10px 0 0 20px; padding: 0;">
                            `;
                            
                            solicitud.productos.forEach((producto, index) => {
                                contenidoHTML += `
                                    <li style="margin-bottom: 5px; padding: 5px; background-color: #f9f9f9; border-radius: 4px;">
                                        ${index + 1}. ${producto.nombre || 'Producto'} - 
                                        Cantidad: ${producto.un_deseadas || 0} 
                                        ${producto.medida || ''}
                                        ${producto.nombre_tipo ? `(${producto.nombre_tipo})` : ''}
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
                        console.log("Respuesta cruda:", xhr.responseText);
                        console.log("Status:", status);
                        console.log("Error:", error);
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
        
    </script>
</body>
</html>