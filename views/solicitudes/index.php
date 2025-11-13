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
                <button class="btn-primary" onclick="openModal('add')" id="newRequestBtn">
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
                                <th scope="col">ID Solicitud</th>
                                <th scope="col">Departamento</th>
                                <th scope="col">Producto</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Fecha requerida</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <?php 
                            // Datos de ejemplo para desarrollo
                            $solicitudes_ejemplo = [
                                [
                                    'id_solicitud' => 'SOL-2023-001',
                                    'departamento' => 'Recursos Humanos',
                                    'producto' => 'Lapiceros azules',
                                    'cantidad' => 50,
                                    'fecha_requerida' => '2025-11-20',
                                    'estado' => 'Pendiente',
                                    'nombre_producto' => 'Lapiceros azules',
                                    'unidad_medida' => 'Unidades',
                                    'tipo_producto' => 'Oficina',
                                    'notas' => 'Para entrega a nuevo personal'
                                ]
                            ];
                            
                            $solicitudes = !empty($solicitudes) ? $solicitudes : $solicitudes_ejemplo;
                            ?>
                            
                            <?php if(!empty($solicitudes)): ?>
                                <?php foreach($solicitudes as $solicitud): ?>
                                    <tr data-status="<?= htmlspecialchars($solicitud['estado']) ?>" 
                                        data-id="<?= htmlspecialchars($solicitud['id_solicitud']) ?>">
                                        <td><?= htmlspecialchars($solicitud['id_solicitud']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['departamento']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['producto']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['cantidad']) ?></td>
                                        <td><?= htmlspecialchars($solicitud['fecha_requerida']) ?></td>

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
                        <button class="btn-primary" onclick="openModal('add')">
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
        <div class="modal-backdrop" onclick="closeModal()"></div>
        <div class="modal-content" role="document">
            <div class="modal-header">
                <h2 id="modalTitle">Nueva Solicitud</h2>
                <button type="button" class="close-btn" onclick="closeModal()" aria-label="Cerrar modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="requestForm" name="requestForm" novalidate>
                <input type="hidden" id="requestId">
                <div class="modal-body">
                    <div class="form-grid">
                        <?php
                        $departamento = $_SESSION['dpto'];
                        ?>
                        <div class="form-group">
                        Departamento que lo solicita <br>
                        <select name="oficina" class="form-select-sm">
                            <option value=1 <?= $departamento == "Biblioteca" ? "selected" : "" ?>>Biblioteca</option>
                            <option value=2 <?= $departamento == "Informatica" ? "selected" : "" ?>>Informatica</option>
                            <option value=3 <?= $departamento == "Cuentas" ? "selected" : "" ?>>Cuentas</option>
                            <option value=4 <?= $departamento == "Deportes" ? "selected" : "" ?>>Deportes</option>
                            <option value=5 <?= $departamento == "Consejeria/Orientacion" ? "selected" : "" ?>>Consejeria/Orientacion</option>
                            <option value=0 <?= $departamento == "Servicios Generales" ? "selected" : "" ?>>Servicios Generales</option>
                        </select>
                        </div>
                        <div class="form-grid">
                            <h3 class="section-title">Información del Producto</h3>
                        </div>
                        <select class="filter-select" name="producto" id="producto">
                            <option value="">-- Ingresar nuevo producto --</option>
                            <?php
                            if ($resultado->num_rows > 0) {
                                while($fila = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $fila['id_producto'] . "'>" . $fila['nombre'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="form-group">
                            <label for="productName" class="required">Nombre del Producto</label>
                            <input type="text" id="productName" name="nombre_producto" required 
                                   placeholder="Ingrese el nombre del producto">
                            <div class="form-error" id="productNameError"></div>
                        </div>

                        <div class="form-group">
                            <label for="productMeasure" class="required">Unidad de Medida</label>
                            <select id="productMeasure" name="unidad_medida" required>
                                <option value="">Seleccionar unidad</option>
                                <option value="Unidades">Unidades</option>
                                <option value="Kilogramos">Kilogramos</option>
                                <option value="Litros">Litros</option>
                                <option value="Cajas">Cajas</option>
                                <option value="Paquetes">Paquetes</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <div class="form-error" id="measureError"></div>
                        </div>

                        <div class="form-group">
                            <label for="requestQuantity" class="required">Cantidad</label>
                            <input type="number" id="requestQuantity" name="cantidad" required 
                                   placeholder="Ej: 10" min="1" max="9999">
                            <div class="form-error" id="quantityError"></div>
                        </div>

                        <div class="form-group">
                            <label for="productType" class="required">Tipo de Producto</label>
                            <select id="productType" name="tipo_producto" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Alimento">Alimento</option>
                                <option value="Limpieza">Limpieza</option>
                                <option value="Electronicos">Electrónicos</option>
                                <option value="Oficina">Oficina</option>
                                <option value="Material literario">Material literario</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <div class="form-error" id="typeError"></div>
                        </div>

                        <div class="form-group">
                            <!--  Etiqueta para fecha de entrega -->
                            <label for="requestDate" class="required">Fecha deseada de entrega del producto</label>
                            <input type="date" id="requestDate" name="fecha_requerida" required
                                   min="<?= date('Y-m-d') ?>">
                            <div class="form-error" id="dateError"></div>
                        </div>
                        <!--  Etiqueta para fecha de entrega -->
                        <div class="form-group full-width">
                            <label for="requestNotes">Notas Adicionales</label>
                            <textarea id="requestNotes" name="notas" rows="3" 
                                      placeholder="Información adicional sobre la solicitud, justificación, urgencia, etc..."
                                      maxlength="500"></textarea>
                            <div class="char-count">
                                <span id="charCount">0</span>/500 caracteres
                            </div>
                        </div>
                    </div>
                </div>
                <!--  Cancelar y Guardar Solicitud -->
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <span class="btn-text">Guardar Solicitud</span>
                        <div class="btn-loading" style="display: none;">
                            <div class="spinner"></div>
                            Guardando...
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        class RequestManager {
            constructor() {
                this.requests = <?= json_encode($solicitudes ?? []) ?>;
                this.currentRequestId = null;
                this.isSubmitting = false;
                this.init();
            }

            init() {
                this.bindEvents();
                this.setDefaultDate();
                this.updateEmptyState();
            }

            bindEvents() {
                // Delegación de eventos para botones de acción
                document.getElementById('requestsTableBody').addEventListener('click', (e) => {
                    const button = e.target.closest('.btn-action');
                    if (!button) return;

                    const action = button.dataset.action;
                    const requestId = button.dataset.id;

                    switch(action) {
                        case 'view':
                            this.viewRequest(requestId);
                            break;
                        case 'approve':
                            this.confirmAction(requestId, 'approve');
                            break;
                        case 'reject':
                            this.confirmAction(requestId, 'reject');
                            break;
                    }
                });

                // Búsqueda y filtros
                const searchInput = document.getElementById('searchInput');
                const statusFilter = document.getElementById('statusFilter');
                
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.filterRequests();
                    }, 300);
                });

                statusFilter.addEventListener('change', () => {
                    this.filterRequests();
                });

                // Formulario
                document.getElementById('requestForm').addEventListener('submit', (e) => {
                    this.saveRequest(e);
                });

                // Validación en tiempo real
                this.setupFormValidation();

                // Cerrar modal
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.closeModal();
                    }
                });

                // Contador de caracteres
                document.getElementById('requestNotes').addEventListener('input', (e) => {
                    this.updateCharCount(e.target.value.length);
                });
            }

            setupFormValidation() {
                const form = document.getElementById('requestForm');
                const fields = form.querySelectorAll('input[required], select[required]');
                
                fields.forEach(field => {
                    field.addEventListener('blur', () => this.validateField(field));
                    field.addEventListener('input', () => this.clearFieldError(field));
                });
            }

            validateField(field) {
                const errorElement = document.getElementById(field.id + 'Error');
                
                if (!field.value.trim()) {
                    this.showFieldError(field, 'Este campo es requerido');
                    return false;
                }

                if (field.type === 'number' && field.value <= 0) {
                    this.showFieldError(field, 'La cantidad debe ser mayor a 0');
                    return false;
                }

                // Cambio: Permitir fechas futuras para la entrega
                if (field.id === 'requestDate') {
                    const selectedDate = new Date(field.value);
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    if (selectedDate < today) {
                        this.showFieldError(field, 'La fecha de entrega no puede ser en el pasado');
                        return false;
                    }
                }

                this.clearFieldError(field);
                return true;
            }

            showFieldError(field, message) {
                const errorElement = document.getElementById(field.id + 'Error');
                errorElement.textContent = message;
                field.classList.add('error');
            }

            clearFieldError(field) {
                const errorElement = document.getElementById(field.id + 'Error');
                errorElement.textContent = '';
                field.classList.remove('error');
            }

            updateCharCount(count) {
                document.getElementById('charCount').textContent = count;
            }

            setDefaultDate() {
                // Establecer fecha mínima como hoy para la fecha de entrega
                const today = new Date();
                document.getElementById('requestDate').min = today.toISOString().split('T')[0];
                // Opcional: establecer una fecha por defecto (ej: 7 días en el futuro)
                const nextWeek = new Date(today);
                nextWeek.setDate(today.getDate() + 7);
                document.getElementById('requestDate').valueAsDate = nextWeek;
            }

            filterRequests() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const statusFilter = document.getElementById('statusFilter').value;
                
                const rows = document.querySelectorAll('#requestsTableBody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.classList.contains('no-data')) return;

                    const text = row.textContent.toLowerCase();
                    const status = row.dataset.status;
                    
                    const matchesSearch = !searchTerm || text.includes(searchTerm);
                    const matchesStatus = !statusFilter || status === statusFilter;
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                this.updateEmptyState(visibleCount === 0);
            }

            updateEmptyState(show = false) {
                const emptyState = document.getElementById('emptyState');
                const table = document.getElementById('requestsTable');
                const hasData = this.requests.length > 0;
                
                if (show || !hasData) {
                    emptyState.style.display = 'flex';
                    table.style.display = 'none';
                } else {
                    emptyState.style.display = 'none';
                    table.style.display = 'table';
                }
            }

            viewRequest(requestId) {
                this.openModal('view', requestId);
            }

            openModal(mode, requestId = null) {
                const modal = document.getElementById('requestModal');
                const modalTitle = document.getElementById('modalTitle');
                const form = document.getElementById('requestForm');
                const footer = document.querySelector('.modal-footer');
                
                // Reset form
                form.reset();
                this.clearAllErrors();
                this.updateCharCount(0);

                if (mode === 'add') {
                    modalTitle.textContent = 'Nueva Solicitud';
                    document.getElementById('requestId').value = '';
                    this.setFormEditable(true);
                    footer.style.display = 'flex';
                    this.setDefaultDate();
                } else if (mode === 'view' && requestId) {
                    modalTitle.textContent = 'Detalles de Solicitud';
                    this.currentRequestId = requestId;
                    
                    const request = this.requests.find(r => r.id_solicitud == requestId);
                    
                    if (request) {
                        document.getElementById('requestId').value = request.id_solicitud;
                        document.getElementById('requestDepartment').value = request.departamento || '';
                        document.getElementById('productName').value = request.nombre_producto || request.producto || '';
                        document.getElementById('productMeasure').value = request.unidad_medida || '';
                        document.getElementById('requestQuantity').value = request.cantidad || '';
                        document.getElementById('productType').value = request.tipo_producto || '';
                        document.getElementById('requestDate').value = request.fecha_requerida || '';
                        document.getElementById('requestNotes').value = request.notas || '';
                        this.updateCharCount(request.notas?.length || 0);
                        
                        this.setFormEditable(false);
                        footer.style.display = 'none';
                    } else {
                        this.showError('No se pudo cargar la información de la solicitud');
                        return;
                    }
                }
                
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            setFormEditable(editable) {
                const formElements = document.querySelectorAll('#requestForm input, #requestForm select, #requestForm textarea');
                formElements.forEach(el => {
                    el.disabled = !editable;
                    el.classList.toggle('disabled', !editable);
                });
            }

            closeModal() {
                const modal = document.getElementById('requestModal');
                modal.classList.remove('active');
                document.body.style.overflow = '';
                this.isSubmitting = false;
                this.resetSubmitButton();
            }

            async saveRequest(event) {
                event.preventDefault();
                
                if (this.isSubmitting) return;
                
                // Validar todos los campos
                const isValid = this.validateForm();
                if (!isValid) return;

                this.setSubmitting(true);

                try {
                    const formData = new FormData();
                    const requestId = document.getElementById('requestId').value;
                    
                    // Agregar datos del formulario
                    formData.append('departamento', document.getElementById('requestDepartment').value.trim());
                    formData.append('nombre_producto', document.getElementById('productName').value.trim());
                    formData.append('unidad_medida', document.getElementById('productMeasure').value);
                    formData.append('cantidad', document.getElementById('requestQuantity').value);
                    formData.append('tipo_producto', document.getElementById('productType').value);
                    formData.append('fecha_requerida', document.getElementById('requestDate').value);
                    formData.append('notas', document.getElementById('requestNotes').value.trim());

                    const endpoint = requestId ? 
                        '?action=solicitud&method=updateSolicitud' : 
                        '?action=solicitud&method=addSolicitud';

                    if (requestId) {
                        formData.append('id', requestId);
                    }

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showSuccess(
                            requestId ? 'Solicitud actualizada correctamente' : 'Solicitud agregada correctamente'
                        );
                        this.closeModal();
                        
                        // Recargar después de éxito
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Error al guardar la solicitud');
                    }
                    
                } catch (error) {
                    console.error('Error:', error);
                    this.showError(error.message || 'Error al guardar la solicitud');
                } finally {
                    this.setSubmitting(false);
                }
            }

            validateForm() {
                const requiredFields = [
                    'requestDepartment', 'productName', 'productMeasure', 
                    'requestQuantity', 'productType', 'requestDate'
                ];
                
                let isValid = true;

                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!this.validateField(field)) {
                        isValid = false;
                    }
                });

                return isValid;
            }

            clearAllErrors() {
                const errorElements = document.querySelectorAll('.form-error');
                errorElements.forEach(el => el.textContent = '');
                
                const fields = document.querySelectorAll('.error');
                fields.forEach(field => field.classList.remove('error'));
            }

            setSubmitting(submitting) {
                this.isSubmitting = submitting;
                const submitBtn = document.getElementById('submitBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (submitting) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'flex';
                    submitBtn.disabled = true;
                } else {
                    btnText.style.display = 'block';
                    btnLoading.style.display = 'none';
                    submitBtn.disabled = false;
                }
            }

            resetSubmitButton() {
                const submitBtn = document.getElementById('submitBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                btnText.style.display = 'block';
                btnLoading.style.display = 'none';
                submitBtn.disabled = false;
            }

            async confirmAction(requestId, action) {
                const actionText = action === 'approve' ? 'aprobar' : 'rechazar';
                const result = await Swal.fire({
                    title: `¿Estás seguro?`,
                    text: `Vas a ${actionText} la solicitud ${requestId}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: action === 'approve' ? '#2ecc71' : '#e74c3c',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: `Sí, ${actionText}`,
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    await this.changeStatus(requestId, action === 'approve' ? 'Aprobado' : 'Rechazado');
                }
            }

            async changeStatus(id, status) {
                try {
                    this.showLoading(true);
                    
                    const response = await fetch('?action=solicitud&method=changeStatus', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id, status: status })
                    });

                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showSuccess(`Solicitud ${status.toLowerCase()} correctamente`);
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Error al cambiar el estado');
                    }
                    
                } catch (error) {
                    console.error('Error:', error);
                    this.showError(error.message || 'Error al cambiar el estado');
                } finally {
                    this.showLoading(false);
                }
            }

            showLoading(show) {
                const loadingState = document.getElementById('loadingState');
                const tableContainer = document.querySelector('.table-container');
                
                if (show) {
                    loadingState.style.display = 'flex';
                    tableContainer.style.opacity = '0.6';
                } else {
                    loadingState.style.display = 'none';
                    tableContainer.style.opacity = '1';
                }
            }

            showSuccess(message) {
                return Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }

            showError(message) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'Entendido'
                });
            }
        }

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            window.requestManager = new RequestManager();
        });

        // Funciones globales para compatibilidad con HTML onclick
        function openModal(mode, requestId = null) {
            window.requestManager.openModal(mode, requestId);
        }

        function closeModal() {
            window.requestManager.closeModal();
        }
    </script>
</body>
</html>