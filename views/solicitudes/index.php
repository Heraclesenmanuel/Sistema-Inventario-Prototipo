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
                        <option value="Aprobado">Aprobado</option>
                        <option value="Rechazado">Rechazado</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="requests-table" id="requestsTable" aria-label="Lista de solicitudes">
                        <thead>
                            <tr>
                                <th scope="col">Departamento</th>
                                <th scope="col">Solicitante</th>
                                <th scope="col">Fecha deseada</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <?php 
                            // Datos de ejemplo para desarrollo
                            $solicitudes_ejemplo = [
                                [
                                    'id_solicitud' => 1,
                                    'nombre_oficina' => 'Recursos Humanos',
                                    'fecha_deseo' => '2025-11-20',
                                    'estado' => 'Pendiente',
                                    'nombre_solicitante' => 'Heracles',
                                    'notas' => 'Para entrega a nuevo personal'
                                ]
                            ];
                            
                            $solicitudes = !empty($solicitudes) ? $solicitudes : $solicitudes_ejemplo;
                            ?>
                            
                            <?php if($solicts_no_en_rev > 0): ?>
                                <?php foreach($solicitudes as $solicitud): ?>
                                    <?php if($solicitud['estado'] !== 'En revision'): ?>
                                    <tr data-status="<?= htmlspecialchars($solicitud['estado']) ?>" 
                                        data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>">
                                        <td><?= htmlspecialchars($solicitud['nombre_oficina']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['nombre_solicitante']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['fecha_deseo']) ?></td>

                                        <td>
                                            <span class="status-badge status-<?= strtolower($solicitud['estado']) ?>">
                                                <?= htmlspecialchars($solicitud['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if($solicitud['estado'] === 'Pendiente'): ?>
                                                    <button type="button" class="btn-action approve" 
                                                            data-action="approve" 
                                                            data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                                            aria-label="Aprobar solicitud">
                                                        <i class="fas fa-check"></i>
                                                        <span>Aprobar</span>
                                                    </button>
                                                    <button type="button" class="btn-action reject" 
                                                            data-action="reject"
                                                            data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                                            aria-label="Rechazar solicitud">
                                                        <i class="fas fa-times"></i>
                                                        <span>Rechazar</span>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                                <button type="button" class="btn-action view" 
                                                        data-action="view"
                                                        data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>"
                                                        aria-label="Ver detalles de solicitud">
                                                    <i class="fas fa-eye"></i>
                                                    <span>Ver</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
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
                            <label for="departamento" class="required">Departamento que lo solicita</label>
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
                                
                                <!-- REEMPLAZAR todo el bloque del product-fields-grid por esto: -->
<div class="product-fields-grid">
    <div class="form-group">
        <label for="producto_0">Producto existente</label>
        <select class="filter-select" name="producto_id[]" id="producto_0">
            <option value="">-- Seleccionar producto existente --</option>
            <?php
            if ($resultado->num_rows > 0) {
                while($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila['id_producto'] . "'>" . $fila['nombre'] . "</option>";
                }
            }
            ?>
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
            
            counter.textContent = `${count} producto${count !== 1 ? 's' : ''}`;
            
            // Mostrar advertencia cuando se acerque al límite
            if (count >= 8) {
                maxHint.style.display = 'block';
            } else {
                maxHint.style.display = 'none';
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
        
    </script>
</body>
</html>