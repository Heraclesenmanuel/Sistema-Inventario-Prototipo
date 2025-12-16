<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de Oficinas - UPEL">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?? 'Inicio' ?> - <?= $titulo ?></title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/oficinas.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php'; ?>
        
        <main class="main-content">
            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <i data-lucide="building-2" class="header-icon"></i>
                    <div>
                        <h1><?= $titulo ?></h1>
                        <p class="header-date">
                            <i data-lucide="calendar" class="date-icon"></i>
                            <span>Hoy es: <?= APP_Date ?></span>
                        </p>
                    </div>
                </div>
            </header>

            <?php if (isset($_GET['exito'])): ?>
                <div class="alert-box alert-success">
                    <i data-lucide="check-circle" class="alert-icon"></i>
                    <div class="alert-content">
                        <strong>¡Éxito!</strong>
                        <p>Tu solicitud fue agregada exitosamente</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Sección de Registro de Oficina -->
            <section class="config-section">
                <div class="modal-header" style="padding: 0 0 24px 0; border: none;">
                    <h2>
                        <i data-lucide="plus-square" class="modal-icon"></i>
                        Agregar Nueva Oficina
                    </h2>
                </div>

                <form action="?action=oficinas&method=home" method="post" id="oficina-form">
                    <!-- Datos de la oficina -->
                    <div class="form-group">
                        <label class="form-label"><i data-lucide="file-text" class="label-icon"></i> Datos de la Oficina</label>
                        <div class="input-group-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                            <div class="input-wrapper">
                                <i data-lucide="hash" class="input-icon"></i>
                                <input type="number" id="num_oficina" name="num_oficina" class="form-input" placeholder="Número de oficina (100-399)" min="100" max="399" required>
                            </div>
                            <div class="input-wrapper">
                                <i data-lucide="type" class="input-icon"></i>
                                <input type="text" id="name" name="name" class="form-input" placeholder="Nombre de la oficina" required>
                            </div>
                            <div class="input-wrapper">
                                <i data-lucide="phone" class="input-icon"></i>
                                <input type="text" id="cel" name="cel" class="form-input" placeholder="Teléfono de oficina" required>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones para el director -->
                    <div class="form-group">
                        <label class="form-label"><i data-lucide="user-cog" class="label-icon"></i> Asignación de Director</label>
                        <div class="director-options">
                            <button type="button" class="director-btn" id="btn-select-director">
                                <i data-lucide="user-check" class="btn-icon"></i>
                                Seleccionar Director Existente
                            </button>
                            <button type="button" class="director-btn" id="btn-new-director">
                                <i data-lucide="user-plus" class="btn-icon"></i>
                                Agregar Nuevo Director
                            </button>
                        </div>
                    </div>

                    <!-- Contenedor del select de directores -->
                    <div class="director-form-container" id="select-director-container">
                        <div class="form-group">
                            <label for="dir_cedula" class="form-label">
                                <i data-lucide="list" class="label-icon"></i>
                                <span>Seleccionar Director<span class="required">*</span></span>
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <div class="input-wrapper" style="flex: 1;">
                                    <select id="dir_cedula" name="dir_cedula" class="form-input" style="appearance: auto;">
                                        <option value="" selected disabled>Seleccione el director de la oficina</option>
                                        <?php if(isset($directores['success']) && $directores['success'] && !empty($directores['data'])): ?>
                                            <?php foreach($directores['data'] as $director): ?>
                                                <option value="<?php echo $director['ced_dir']?>" 
                                                        data-nombre="<?php echo htmlspecialchars($director['nombre'])?>"
                                                        data-telf="<?php echo htmlspecialchars($director['telf'])?>">
                                                    <?php echo htmlspecialchars($director['nombre'])?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <button type="button" class="btn-secondary" style="padding: 14px;" onclick="window.location.href='?action=oficinas&method=directores'" title="Gestionar Directores">
                                    <i data-lucide="external-link" class="btn-icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de datos del director -->
                    <div class="director-form-container" id="director-data-container">
                        <div class="form-group">
                            <label class="form-label"><i data-lucide="user" class="label-icon"></i> Datos del Director</label>
                            <div class="input-group-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                                <div class="input-wrapper">
                                    <i data-lucide="credit-card" class="input-icon"></i>
                                    <input type="text" id="cedula" name="cedula" class="form-input" placeholder="Cédula" required readonly>
                                </div>
                                <div class="input-wrapper">
                                    <i data-lucide="user" class="input-icon"></i>
                                    <input type="text" id="dir_nombre" name="dir_nombre" class="form-input" placeholder="Nombre completo" required readonly>
                                </div>
                                <div class="input-wrapper">
                                    <i data-lucide="phone" class="input-icon"></i>
                                    <input type="text" id="dir_telf" name="dir_telf" class="form-input" placeholder="Teléfono" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" id="hidden_cedula" name="cedula" value="">
                    <input type="hidden" id="hidden_dir_nombre" name="dir_nombre" value="">
                    <input type="hidden" id="hidden_dir_telf" name="dir_telf" value="">
                    <input type="hidden" id="modo_director" name="modo_director" value="">

                    <div class="form-group" style="margin-top: 24px;">
                        <button type="submit" id="btn-add" name="btn-add" class="btn-submit">
                            <i data-lucide="save" class="btn-icon"></i>
                            <span>Guardar Oficina</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Sección de Listado -->
            <section class="config-section">
                <div class="modal-header" style="padding: 0 0 24px 0; border: none;">
                    <h2>
                        <i data-lucide="list" class="modal-icon"></i>
                        Oficinas Registradas
                    </h2>
                </div>

                <!-- Barra de búsqueda y paginación -->
                <div class="search-actions-bar">
                    <div class="search-box">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" id="buscar" class="search-input" placeholder="Buscar por nombre, cédula o teléfono...">
                    </div>
                    <div class="entries-selector">
                        <label for="entries-select">Mostrar:</label>
                        <select id="entries-select" class="entries-select">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="all">Todos</option>
                        </select>
                    </div>
                </div>

                <div class="table-container">
                    <table class="users-table" id="tabla-clientes">
                        <thead>
                            <tr>
                                <th>NRO Oficina</th>
                                <th>Nombre</th>
                                <th>Director</th>
                                <th>Contacto Oficina</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($oficinas)): ?>
                                <?php foreach($oficinas as $oficina): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($oficina['num_oficina']); ?></td>
                                        <td><?php echo htmlspecialchars($oficina['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($oficina['nombre_dir']); ?></td>
                                        <td><?php echo htmlspecialchars($oficina['telefono']); ?></td>
                                        <td>
                                            <div class="action-buttons-cell">
                                                <button class="btn-edit" 
                                                    title="Editar" 
                                                    onclick='openEditOfficeModal(<?php echo json_encode($oficina); ?>)'>
                                                    <i data-lucide="pencil" class="btn-icon"></i>
                                                </button>
                                                <button class="btn-view" 
                                                    title="Ver detalles" 
                                                    onclick='openOfficeModal(<?php echo json_encode($oficina); ?>)'>
                                                    <i data-lucide="eye" class="btn-icon"></i>
                                                </button>
                                                <button class="btn-delete"
                                                    title="Eliminar oficina" 
                                                    data-id="<?php echo $oficina['num_oficina']; ?>">
                                                    <i data-lucide="trash-2" class="btn-icon"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">
                                        <i data-lucide="building" class="no-data-icon"></i>
                                        <p>No hay Oficinas registradas</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Navigation -->
                <div class="pagination-controls-bottom">
                    <div class="pagination-info" id="pagination-info"></div>
                    <nav class="pagination-nav" id="pagination"></nav>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Detalles de Oficina -->
    <div id="officeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>
                    <i data-lucide="building-2" class="modal-icon"></i>
                    Detalles de la Oficina
                </h2>
                <button class="modal-close" id="closeModal">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="info-section">
                    <h4 class="form-label"><i data-lucide="info" class="label-icon"></i> Información General</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                        <div>
                            <small style="color: var(--texto-secundario);">Número</small>
                            <div id="modal-num-oficina" style="font-weight: 600;">-</div>
                        </div>
                        <div>
                            <small style="color: var(--texto-secundario);">Nombre</small>
                            <div id="modal-nombre" style="font-weight: 600;">-</div>
                        </div>
                        <div>
                            <small style="color: var(--texto-secundario);">Teléfono</small>
                            <div id="modal-telefono" style="font-weight: 600;">-</div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h4 class="form-label"><i data-lucide="user-tie" class="label-icon"></i> Información del Director</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                        <div>
                            <small style="color: var(--texto-secundario);">Nombre</small>
                            <div id="modal-director-nombre" style="font-weight: 600;">-</div>
                        </div>
                        <div>
                            <small style="color: var(--texto-secundario);">Cédula</small>
                            <div id="modal-director-cedula" style="font-weight: 600;">-</div>
                        </div>
                        <div>
                            <small style="color: var(--texto-secundario);">Teléfono</small>
                            <div id="modal-director-telefono" style="font-weight: 600;">-</div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h4 class="form-label"><i data-lucide="calendar" class="label-icon"></i> Información Adicional</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <small style="color: var(--texto-secundario);">Fecha Creación</small>
                            <div id="modal-fecha-creacion" style="font-weight: 600;"><?php echo date('d/m/Y'); ?></div>
                        </div>
                        <div>
                            <small style="color: var(--texto-secundario);">Estado</small>
                            <div id="modal-estado" style="font-weight: 600; color: var(--verde-esmeralda);">Activa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edición de Oficina -->
    <div id="editOfficeModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>
                    <i data-lucide="edit-3" class="modal-icon"></i>
                    Editar Oficina
                </h2>
                <button class="modal-close" id="closeEditModal">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <form id="editOfficeForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_num_oficina_original" name="num_oficina_original">
                    
                    <div class="form-group">
                        <label class="form-label"><i data-lucide="hash" class="label-icon"></i> Número de Oficina</label>
                        <div class="input-wrapper">
                            <i data-lucide="hash" class="input-icon"></i>
                            <input type="text" id="edit_num_oficina" name="num_oficina" class="form-input" placeholder="Ej: 101" required>
                        </div>
                        <small class="form-text">Identificador único de la oficina</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i data-lucide="type" class="label-icon"></i> Nombre de la Oficina</label>
                        <div class="input-wrapper">
                            <i data-lucide="type" class="input-icon"></i>
                            <input type="text" id="edit_nombre" name="nombre" class="form-input" placeholder="Ej: Oficina de Sistemas" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i data-lucide="phone" class="label-icon"></i> Teléfono</label>
                        <div class="input-wrapper">
                            <i data-lucide="phone" class="input-icon"></i>
                            <input type="text" id="edit_telefono" name="telefono" class="form-input" placeholder="Ej: 0412-1234567" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i data-lucide="user-tie" class="label-icon"></i> Director</label>
                        <div class="input-wrapper">
                            <i data-lucide="user-tie" class="input-icon"></i>
                            <select id="edit_ced_dir" name="ced_dir" class="form-input" style="appearance: auto;" required>
                                <option value="" selected disabled>Seleccione un director</option>
                                <?php if(isset($directores['success']) && $directores['success'] && !empty($directores['data'])): ?>
                                    <?php foreach($directores['data'] as $director): ?>
                                        <option value="<?php echo $director['ced_dir']?>">
                                            <?php echo htmlspecialchars($director['nombre']) . " (" . htmlspecialchars($director['ced_dir']) . ")" ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small class="form-text">Asignar director responsable</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelEditBtn">
                        <i data-lucide="x" class="btn-icon"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn-submit">
                        <i data-lucide="save" class="btn-icon"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Lucide
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Referencias DOM
            const officeModal = document.getElementById('officeModal');
            const closeModal = document.getElementById('closeModal');
            const editOfficeModal = document.getElementById('editOfficeModal');
            const closeEditModal = document.getElementById('closeEditModal');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const editOfficeForm = document.getElementById('editOfficeForm');

            window.openEditOfficeModal = function(oficinaData) {
                if (typeof oficinaData === 'string') oficinaData = JSON.parse(oficinaData);
                
                // Llenar los campos del formulario de edición
                document.getElementById('edit_num_oficina_original').value = oficinaData.num_oficina;
                document.getElementById('edit_num_oficina').value = oficinaData.num_oficina || '';
                document.getElementById('edit_nombre').value = oficinaData.nombre || '';
                document.getElementById('edit_telefono').value = oficinaData.telefono || '';
                
                // Establecer el director seleccionado
                const directorSelect = document.getElementById('edit_ced_dir');
                if (oficinaData.ced_dir) {
                    directorSelect.value = oficinaData.ced_dir;
                }
                // Mostrar modal
                editOfficeModal.style.display = 'flex';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            };

            // Cerrar modal de edición
            closeEditModal.addEventListener('click', () => {
                editOfficeModal.style.display = 'none';
            });

            cancelEditBtn.addEventListener('click', () => {
                editOfficeModal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === editOfficeModal) editOfficeModal.style.display = 'none';
            });
            
            // Enviar formulario de edición
            editOfficeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                // Mostrar confirmación
                Swal.fire({
                    title: '¿Guardar cambios?',
                    text: "Se actualizarán los datos de la oficina.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3F51B5',
                    cancelButtonColor: '#5F6368',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar datos por fetch
                        fetch('?action=oficinas&method=editarOficina', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: data.message || 'Oficina actualizada correctamente.',
                                    icon: 'success',
                                    confirmButtonColor: '#3F51B5'
                                }).then(() => {
                                    // Cerrar modal y recargar la página
                                    editOfficeModal.style.display = 'none';
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.error || 'No se pudo actualizar la oficina.',
                                    icon: 'error',
                                    confirmButtonColor: '#E44336'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al procesar la solicitud.',
                                icon: 'error',
                                confirmButtonColor: '#E44336'
                            });
                        });
                    }
                });
            });

            // ... resto de tu código existente (lógica de director, paginación, etc.) ...

            // ===== Modal Detalles (existente) =====
            window.openOfficeModal = function(oficinaData) {
                if (typeof oficinaData === 'string') oficinaData = JSON.parse(oficinaData);
                
                document.getElementById('modal-num-oficina').textContent = oficinaData.num_oficina || '-';
                document.getElementById('modal-nombre').textContent = oficinaData.nombre || '-';
                document.getElementById('modal-telefono').textContent = oficinaData.telefono || '-';
                document.getElementById('modal-director-nombre').textContent = oficinaData.nombre_dir || '-';
                document.getElementById('modal-director-cedula').textContent = oficinaData.ced_dir || '-';
                document.getElementById('modal-director-telefono').textContent = oficinaData.telf_dir || '-';
                
                officeModal.style.display = 'flex';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            };

            closeModal.addEventListener('click', () => {
                officeModal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === officeModal) officeModal.style.display = 'none';
            });

            // ===== Paginación y Búsqueda (Versión Robusta) =====
            const buscarInput = document.getElementById('buscar');
            const entriesSelect = document.getElementById('entries-select');
            const tbody = document.querySelector('#tabla-clientes tbody');
            const paginationInfo = document.getElementById('pagination-info');
            const paginationContainer = document.getElementById('pagination');
            
            // Obtener todas las filas originales. Ignoramos la fila de 'no-data' si existe.
            const allRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('no-data'));
            
            let currentPage = 1;
            let rowsPerPage = 5;

            function updatePagination() {
                const term = buscarInput.value.toLowerCase().trim();
                
                // 1. Filtrar filas
                const visibleRows = allRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(term);
                });

                const totalRows = visibleRows.length;
                
                // 2. Calcular paginación
                const totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(totalRows / rowsPerPage);
                if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
                
                const startIndex = rowsPerPage === 'all' ? 0 : (currentPage - 1) * rowsPerPage;
                const endIndex = rowsPerPage === 'all' ? totalRows : Math.min(startIndex + rowsPerPage, totalRows);

                // 3. Mostrar/Ocultar filas
                allRows.forEach(row => row.style.display = 'none'); // Ocultar todas primero
                
                // Mostrar solo las de la página actual
                for (let i = startIndex; i < endIndex; i++) {
                    if (visibleRows[i]) {
                        visibleRows[i].style.display = '';
                    }
                }

                // 4. Manejo de "No data"
                // Eliminar mensaje no-data anterior si existe (pero no tocar las filas originales ocultas)
                let noDataRow = tbody.querySelector('.no-data-row');
                if (noDataRow) noDataRow.remove();

                if (totalRows === 0 && allRows.length > 0) { // Si hay filas pero el filtro las ocultó todas
                     const tr = document.createElement('tr');
                     tr.className = 'no-data-row';
                     tr.innerHTML = `<td colspan="5" class="no-data"><i data-lucide="search" class="no-data-icon"></i><p>No se encontraron resultados</p></td>`;
                     tbody.appendChild(tr);
                     if (typeof lucide !== 'undefined') lucide.createIcons();
                }

                // 5. Actualizar Info y Controles
                if (totalRows > 0) {
                    paginationInfo.textContent = `Mostrando ${startIndex + 1} a ${endIndex} de ${totalRows} oficinas`;
                } else {
                    paginationInfo.textContent = '';
                }
                
                renderPaginationControls(totalPages);
            }

            function renderPaginationControls(totalPages) {
                paginationContainer.innerHTML = '';
                if (totalPages <= 1) return;

                const createBtn = (text, icon, onClick, disabled, isActive = false) => {
                    const btn = document.createElement('button');
                    btn.className = isActive ? 'pagination-number active' : 'pagination-btn';
                    if (isActive) btn.className = 'pagination-number active'; // Override for numbers
                    else if (!icon) btn.className = 'pagination-number'; // Normal numbers
                    
                    if (icon) btn.innerHTML = `<i data-lucide="${icon}" class="pagination-icon"></i>`;
                    else btn.textContent = text;
                    
                    btn.disabled = disabled;
                    btn.onclick = onClick;
                    return btn;
                };

                // Prev
                paginationContainer.appendChild(createBtn('', 'chevron-left', () => { currentPage--; updatePagination(); }, currentPage === 1));

                // Numbers
                for (let i = 1; i <= totalPages; i++) {
                    paginationContainer.appendChild(createBtn(i, null, () => { currentPage = i; updatePagination(); }, false, i === currentPage));
                }

                // Next
                paginationContainer.appendChild(createBtn('', 'chevron-right', () => { currentPage++; updatePagination(); }, currentPage === totalPages));
                
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            // Event Listeners
            buscarInput.addEventListener('input', () => { currentPage = 1; updatePagination(); });
            entriesSelect.addEventListener('change', function() {
                rowsPerPage = this.value === 'all' ? 'all' : parseInt(this.value);
                currentPage = 1;
                updatePagination();
            });

            // ===== Eliminar (Vinculado una sola vez) =====
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#E44336',
                        cancelButtonColor: '#5F6368',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                             fetch(`?action=oficinas&method=deleteOficina&id=${id}`) // URL Relativa correcta
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Eliminado', 'La oficina se eliminó con éxito.', 'success')
                                        .then(() => window.location.reload());
                                    } else {
                                        Swal.fire('Error', data.error || 'No se pudo eliminar.', 'error');
                                    }
                                })
                                .catch(() => Swal.fire('Error', 'Fallo en conexión.', 'error'));
                        }
                    });
                });
            });

            // Inicializar
            updatePagination();
        });
    </script>
</body>
</html>