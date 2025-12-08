<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de Proveedores - UPEL">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?? 'UPEL' ?> - Gestión de Proveedores</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/proveedores.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>
        
        <main class="main-content">
            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <i data-lucide="truck" class="header-icon"></i>
                    <div>
                        <h2>Gestión de Proveedores</h2>
                        <p class="subtitle">
                            <i data-lucide="users" style="width: 16px; height: 16px;"></i>
                            Administra y organiza tu red de proveedores
                        </p>
                    </div>
                </div>
                <!-- Botón Nuevo Proveedor -->
                <button class="btn-primary" onclick="openModal('add')">
                    <i data-lucide="plus-circle"></i>
                    Nuevo Proveedor
                </button>
            </header>

            <!-- Sección Principal -->
            <section class="config-section">
                <!-- Barra de búsqueda y filtros -->
                <div class="search-filter-bar">
                    <div class="search-box">
                        <i data-lucide="search" class="search-icon"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input"
                            placeholder="Buscar por nombre, RIF o email...">
                    </div>
                    
                    <div class="filter-group">
                        <select id="statusFilter" class="filter-select">
                            <option value="">Todos los estados</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Controles de Paginación Superior -->
                <div class="pagination-controls-top" style="margin-bottom: 1rem; padding: 0 0.5rem;">
                    <div class="entries-selector">
                        <label for="entriesPerPage">Mostrar:</label>
                        <select id="entriesPerPage" class="entries-select">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span>proveedores</span>
                    </div>
                </div>

                <!-- Tabla de Proveedores -->
                <div class="table-container">
                    <table class="providers-table" id="providersTable">
                        <thead>
                            <tr>
                                <th>Proveedor</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th>RIF</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="providersTableBody">
                            <?php if(!empty($proveedores)): ?>
                                <?php foreach($proveedores as $proveedor): ?>
                                    <tr data-status="<?= htmlspecialchars($proveedor['estado']) ?>">
                                        <td style="font-weight: 500;"><?= htmlspecialchars($proveedor['nombre']) ?></td>
                                        <td><?= htmlspecialchars($proveedor['email']) ?></td>
                                        <td>
                                            <a href="https://wa.me/<?= $proveedor['telefono'] ?>" target="_blank" class="phone-link">
                                                <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                                                <?= htmlspecialchars($proveedor['telefono']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower($proveedor['estado']) ?>">
                                                <i data-lucide="<?= $proveedor['estado'] == 'Activo' ? 'check-circle' : 'x-circle' ?>" style="width: 12px; height: 12px;"></i>
                                                <?= htmlspecialchars($proveedor['estado']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($proveedor['rif']) ?></td>
                                        <td>
                                            <div class="action-buttons-cell">
                                                <button class="action-btn btn-edit" onclick="editProvider('<?= $proveedor['rif'] ?>')" title="Editar">
                                                    <i data-lucide="edit-3"></i>
                                                </button>
                                                <button class="action-btn btn-delete" onclick="deleteProvider('<?= $proveedor['rif'] ?>')" title="Eliminar">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Fila No Data (Oculta por defecto, creada dinámicamente si es necesario) -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación Inferior -->
                <div class="pagination-controls-bottom">
                    <div id="paginationInfo" style="font-size: 0.9rem; color: var(--text-secondary);"></div>
                    <nav class="pagination-nav" id="paginationControls"></nav>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Formulario -->
    <div id="providerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">
                    <i data-lucide="plus-circle"></i> Nuevo Proveedor
                </h2>
                <button class="close-btn" onclick="closeModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>
            
            <form id="providerForm" method="post" onsubmit="saveProvider(event)">
                <input type="hidden" id="providerRif" name="rif_original">
                
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="providerName" class="form-label">
                                <i data-lucide="type" style="width: 16px; height: 16px;"></i> Nombre del Proveedor <span class="required">*</span>
                            </label>
                            <input type="text" id="providerName" name="nombre" class="form-input" required placeholder="Ej: Distribuidora ABC">
                        </div>

                        <div class="form-group">
                            <label for="providerEmail" class="form-label">
                                <i data-lucide="mail" style="width: 16px; height: 16px;"></i> Email <span class="required">*</span>
                            </label>
                            <input type="email" id="providerEmail" name="email" class="form-input" required placeholder="contacto@proveedor.com">
                        </div>

                        <div class="form-group">
                            <label for="providerPhone" class="form-label">
                                <i data-lucide="phone" style="width: 16px; height: 16px;"></i> Teléfono <span class="required">*</span>
                            </label>
                            <input type="tel" id="providerPhone" name="telefono" class="form-input" required placeholder="0414 5000000">
                        </div>
                        
                        <div class="form-group">
                            <label for="rif" class="form-label">
                                <i data-lucide="hash" style="width: 16px; height: 16px;"></i> RIF <span class="required">*</span>
                            </label>
                            <input type="text" id="rif" name="rif" class="form-input" required placeholder="Ej: J-12345678">
                        </div>

                        <div class="form-group">
                            <label for="providerStatus" class="form-label">
                                <i data-lucide="activity" style="width: 16px; height: 16px;"></i> Estado <span class="required">*</span>
                            </label>
                            <select id="providerStatus" name="estado" class="form-input" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="providerAddress" class="form-label">
                                <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i> Dirección
                            </label>
                            <input type="text" id="providerAddress" name="direccion" class="form-input" placeholder="Calle, Ciudad, País">
                        </div>
                        
                        <!-- Categorías de Especialización -->
                        <div class="form-group full-width">
                            <label class="form-label">
                                <i data-lucide="tag" style="width: 16px; height: 16px;"></i> Categorías de Especialización
                            </label>
                            <div class="categorias-grid">
                                <?php if(!empty($categorias)): ?>
                                    <?php foreach($categorias['data'] as $categoria): ?>
                                        <div class="categoria-item">
                                            <input type="checkbox" id="cat_<?php echo $categoria['nombre']?>" name="categorias[]" value="<?php echo $categoria['id_tipo']?>">
                                            <label for="cat_<?php echo $categoria['nombre']?>"><?php echo htmlspecialchars($categoria['nombre'])?></label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="providerNotes" class="form-label">
                                <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> Notas
                            </label>
                            <textarea id="providerNotes" name="nota" class="form-input" rows="3" placeholder="Información adicional sobre el proveedor..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Datos iniciales
        let providers = <?= json_encode($proveedores ?? []) ?>;

        // Variables de Paginación
        let currentPage = 1;
        let rowsPerPage = 10;
        
        // Elementos DOM
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const entriesSelect = document.getElementById('entriesPerPage');
        const tbody = document.getElementById('providersTableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');

        // Obtener filas originales (excluyendo mensajes de no-data previos si hubiera)
        // Nota: Al cargar la página, PHP renderiza las filas. Las capturamos todas.
        let allRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('no-data-row'));

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
            updatePagination(); // Iniciar paginación
        });

        // ===== Lógica de Actualización robusta (Mostrar/Ocultar) =====
        function updatePagination() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusTerm = statusFilter.value;

            // 1. Filtrar
            const visibleRows = allRows.filter(row => {
                const text = row.textContent.toLowerCase();
                const status = row.getAttribute('data-status'); // Usar getAttribute para asegurar lectura
                
                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusTerm || status === statusTerm;
                
                return matchesSearch && matchesStatus;
            });

            const totalRows = visibleRows.length;
            
            // 2. Calcular límites
            const pageSize = rowsPerPage === 'all' ? totalRows : parseInt(rowsPerPage);
            const totalPages = Math.ceil(totalRows / pageSize) || 1;
            
            if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, totalRows);

            // 3. Aplicar visibilidad
            // Primero ocultar TODOS los originales
            allRows.forEach(row => row.style.display = 'none');

            // Mostrar solo los del rango actual
            for (let i = startIndex; i < endIndex; i++) {
                if(visibleRows[i]) visibleRows[i].style.display = ''; // Restaurar display por defecto (table-row)
            }

            // 4. Manejo de estado vacío
            let noDataRow = tbody.querySelector('.no-data-row');
            if (noDataRow) noDataRow.remove();

            if (totalRows === 0) {
                const tr = document.createElement('tr');
                tr.className = 'no-data-row';
                tr.innerHTML = `<td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-secondary);">
                                    <div style="display:flex; flex-direction:column; align-items:center; gap:0.5rem;">
                                        <i data-lucide="search" style="width:32px; height:32px; opacity:0.5;"></i>
                                        <p>No se encontraron proveedores</p>
                                    </div>
                                </td>`;
                tbody.appendChild(tr);
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            // 5. Actualizar Info y Controles
            if (totalRows > 0) {
                paginationInfo.textContent = `Mostrando ${startIndex + 1} a ${endIndex} de ${totalRows} proveedores`;
            } else {
                paginationInfo.textContent = '';
            }

            renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            paginationControls.innerHTML = '';
            if (totalPages <= 1) return;

            const createBtn = (active, disabled, icon, text, onClick) => {
                const btn = document.createElement('button');
                btn.className = active ? 'pagination-number active' : 'pagination-btn';
                if (!icon && !active) btn.classList.add('pagination-number');
                
                if (icon) btn.innerHTML = `<i data-lucide="${icon}"></i>`;
                else btn.textContent = text;
                
                btn.disabled = disabled;
                btn.onclick = onClick;
                return btn;
            };

            // Prev
            paginationControls.appendChild(createBtn(false, currentPage === 1, 'chevron-left', '', () => { currentPage--; updatePagination(); }));

            // Números (Simple: mostrar todos por ahora, idealmente usar lógica de truncamiento para muchos números)
            for (let i = 1; i <= totalPages; i++) {
                paginationControls.appendChild(createBtn(i === currentPage, false, null, i, () => { currentPage = i; updatePagination(); }));
            }

            // Next
            paginationControls.appendChild(createBtn(false, currentPage === totalPages, 'chevron-right', '', () => { currentPage++; updatePagination(); }));
            
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        // Event Listeners para Filtros
        searchInput.addEventListener('input', () => { currentPage = 1; updatePagination(); });
        statusFilter.addEventListener('change', () => { currentPage = 1; updatePagination(); });
        entriesSelect.addEventListener('change', function() {
            rowsPerPage = this.value;
            currentPage = 1;
            updatePagination();
        });

        // ===== Lógica de Modales y CRUD =====
        
        // Editar proveedor (Fetch recomendaciones)
        async function editProvider(providerRif) {
            try {
                const response = await fetch(`?action=proveedor&method=getRecomendaciones&rif=${encodeURIComponent(providerRif)}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `rif=${encodeURIComponent(providerRif)}`
                });

                if (!response.ok) throw new Error("Error en la petición: " + response.status);

                const recomendacionesdata = await response.json();
                
                if (!recomendacionesdata.success) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error accediendo a los datos.' });
                } else {
                    openModal('edit', providerRif, recomendacionesdata);
                }
            } catch (error) {
                console.error(error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la información.' });
            }
        }

        function openModal(mode, providerRif = null, recom_data = null) {
            const modal = document.getElementById('providerModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('providerForm');
            
            form.reset();
            // Limpiar checkboxes
            document.querySelectorAll('input[name="categorias[]"]').forEach(cb => cb.checked = false);
            
            if (mode === 'add') {
                modalTitle.innerHTML = '<i data-lucide="plus-circle"></i> Nuevo Proveedor';
                document.getElementById('providerRif').value = '';
            } else if (mode === 'edit' && providerRif) {
                modalTitle.innerHTML = '<i data-lucide="edit-3"></i> Editar Proveedor';
                
                const provider = providers.find(p => p.rif === providerRif);
                
                if (provider) {
                    document.getElementById('providerRif').value = provider.rif;
                    document.getElementById('providerName').value = provider.nombre || '';
                    document.getElementById('providerEmail').value = provider.email || '';
                    document.getElementById('providerPhone').value = provider.telefono || '';
                    document.getElementById('providerStatus').value = provider.estado || 'Activo';
                    document.getElementById('providerAddress').value = provider.direccion || '';
                    document.getElementById('providerNotes').value = provider.nota || '';
                    document.getElementById('rif').value = provider.rif || '';
                    
                    if (recom_data && recom_data.recomendaciones) {
                        for (let nombre_recom of recom_data.recomendaciones) {
                            const checkbox = document.getElementById('cat_' + nombre_recom);
                            if (checkbox) checkbox.checked = true;
                        }
                    }
                }
            }
            
            modal.classList.add('active');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function closeModal() {
            document.getElementById('providerModal').classList.remove('active');
        }

        // Guardar Proveedor
        async function saveProvider(event) {
            event.preventDefault();
            
            const formData = new FormData();
            const providerRif = document.getElementById('providerRif').value;
            const isEdit = !!providerRif;
            
            const categoriasSeleccionadas = [];
            document.querySelectorAll('input[name="categorias[]"]:checked').forEach(cb => {
                categoriasSeleccionadas.push(cb.value);
            });
            
            formData.append('nombre', document.getElementById('providerName').value);
            formData.append('email', document.getElementById('providerEmail').value);
            formData.append('telefono', document.getElementById('providerPhone').value);
            formData.append('estado', document.getElementById('providerStatus').value);
            formData.append('direccion', document.getElementById('providerAddress').value);
            formData.append('nota', document.getElementById('providerNotes').value);
            formData.append('rif', document.getElementById('rif').value);
            formData.append('categorias_seleccionadas', JSON.stringify(categoriasSeleccionadas));
            
            if (isEdit) {
                formData.append('rif_original', providerRif);
            }

            try {
                const endpoint = isEdit ? '?action=proveedor&method=updateProveedor' : '?action=proveedor&method=addProveedor';
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('El servidor no devolvió JSON');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: isEdit ? 'Proveedor actualizado' : 'Proveedor creado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    closeModal();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(result.message || 'Error al guardar');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message });
            }
        }

        // Eliminar Proveedor
        async function deleteProvider(rif) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E44336',
                cancelButtonColor: '#5F6368',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`?action=proveedor&method=eliminarProveedor&rif=${encodeURIComponent(rif)}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `rif=${encodeURIComponent(rif)}`
                    });
                    
                    const text = await response.text();
                    let data;
                    try { 
                        data = JSON.parse(text); 
                    } catch(e) { 
                        throw new Error('Error de servidor'); 
                    }
                    
                    if (data.success) {
                        Swal.fire('Eliminado', 'Proveedor eliminado.', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }

        // Cerrar modal al hacer click fuera
        window.onclick = function(event) {
            const modal = document.getElementById('providerModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>