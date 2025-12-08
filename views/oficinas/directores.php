<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de Directores - UPEL">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?? 'UPEL' ?> - Gestión de Directores</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/oficinas.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>
        
        <main class="main-content">
            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <i data-lucide="user-check" class="header-icon"></i>
                    <div>
                        <h1>Gestión de Directores</h1>
                        <p class="header-date">
                            <i data-lucide="users" class="date-icon"></i>
                            <span>Administra y organiza tu red de directores</span>
                        </p>
                    </div>
                </div>
            </header>

            <!-- Sección Principal -->
            <section class="config-section">
                <!-- Barra de búsqueda y acciones -->
                <div class="search-actions-bar">
                    <div class="search-box">
                        <i data-lucide="search" class="search-icon"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input"
                            placeholder="Buscar por nombre, cédula o teléfono..."
                            onkeyup="filterProviders()">
                    </div>
                    <div class="action-buttons">
                        <button class="btn-action btn-primary" onclick="openModal('add')">
                            <i data-lucide="user-plus" class="btn-icon"></i>
                            <span>Nuevo Director</span>
                        </button>
                    </div>
                </div>

                <!-- Pagination Controls Top -->
                <div class="pagination-controls-top">
                    <div class="entries-selector">
                        <label for="entriesPerPage">Mostrar:</label>
                        <select id="entriesPerPage" class="entries-select" onchange="updatePagination()">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="entries-label">directores</span>
                    </div>
                    <div class="pagination-info">
                        <span id="paginationInfo">Cargando...</span>
                    </div>
                </div>

                <!-- Tabla de Directores -->
                <div class="table-container">
                    <table class="users-table" id="providersTable">
                        <thead>
                            <tr>
                                <th>Director</th>
                                <th>Teléfono</th>
                                <th>Cédula</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="providersTableBody">
                            <?php if(!empty($directores['data'])): ?>
                                <?php foreach($directores['data'] as $director): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($director['nombre']) ?></td>
                                        <td>
                                            <a href="https://wa.me/<?= $director['telf'] ?>" target="_blank" style="color: var(--azul-anil); text-decoration: none; display: flex; align-items: center; gap: 6px;">
                                                <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                                                <?= htmlspecialchars($director['telf']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($director['ced_dir']) ?></td>
                                        <td>
                                            <div class="action-buttons-cell">
                                                <button class="btn-edit" onclick="editProvider('<?= $director['ced_dir'] ?>')" title="Editar">
                                                    <i data-lucide="edit-3" class="btn-icon"></i>
                                                </button>
                                                <button class="btn-delete" onclick="deleteProvider('<?= $director['ced_dir'] ?>')" title="Eliminar">
                                                    <i data-lucide="trash-2" class="btn-icon"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Empty State (Hidden by default) -->
                    <div id="emptyState" class="no-data" style="display: none;">
                        <i data-lucide="users" class="no-data-icon"></i>
                        <p>No se encontraron directores</p>
                    </div>
                </div>

                <!-- Pagination Navigation -->
                <div class="pagination-controls-bottom">
                    <nav class="pagination-nav" aria-label="Paginación">
                        <button class="pagination-btn" id="prevPage" onclick="changePage(-1)">
                            <i data-lucide="chevron-left" class="pagination-icon"></i>
                        </button>
                        <div class="pagination-numbers" id="paginationNumbers"></div>
                        <button class="pagination-btn" id="nextPage" onclick="changePage(1)">
                            <i data-lucide="chevron-right" class="pagination-icon"></i>
                        </button>
                    </nav>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Formulario -->
    <div id="providerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">
                    <i data-lucide="user-plus" class="modal-icon"></i>
                    Nuevo Director
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>
            
            <form id="providerForm" method="post" onsubmit="saveProvider(event)">
                <input type="hidden" id="providerCed" name="ced_original">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dir_nombre" class="form-label">
                            <i data-lucide="user" class="label-icon"></i>
                            <span>Nombre del Director<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i data-lucide="type" class="input-icon"></i>
                            <input type="text" id="dir_nombre" name="nombre" class="form-input" required placeholder="Ej: Juan Pérez">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dir_telf" class="form-label">
                            <i data-lucide="phone" class="label-icon"></i>
                            <span>Teléfono<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i data-lucide="smartphone" class="input-icon"></i>
                            <input type="tel" id="dir_telf" name="telefono" class="form-input" required placeholder="0414 5000000">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cedula" class="form-label">
                            <i data-lucide="credit-card" class="label-icon"></i>
                            <span>Cédula<span class="required">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <i data-lucide="hash" class="input-icon"></i>
                            <input type="text" id="cedula" name="cedula" class="form-input" required placeholder="Ej: 12345678">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-submit">
                        <i data-lucide="check" class="btn-icon"></i>
                        <span>Guardar Director</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let directors = <?= json_encode($directores['data'] ?? []) ?>;
        
        // Variables de paginación
        let currentPage = 1;
        let rowsPerPage = 10;
        let filteredDirectors = [...directors];

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            updatePagination();
        });

        // Filtrar directores
        function filterProviders() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            filteredDirectors = directors.filter(director => {
                const nameMatch = director.nombre.toLowerCase().includes(searchTerm);
                const cedulaMatch = director.ced_dir.toLowerCase().includes(searchTerm);
                const telfMatch = director.telf.toLowerCase().includes(searchTerm);
                return nameMatch || cedulaMatch || telfMatch;
            });
            
            currentPage = 1;
            updatePagination();
        }

        // Sistema de Paginación y Renderizado
        function updatePagination() {
            rowsPerPage = parseInt(document.getElementById('entriesPerPage').value);
            const totalRows = filteredDirectors.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            // Validar página actual
            if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = Math.min(startIndex + rowsPerPage, totalRows);
            const currentRows = filteredDirectors.slice(startIndex, endIndex);

            renderTable(currentRows);
            renderPaginationControls(totalPages, startIndex, endIndex, totalRows);
            
            // Actualizar iconos
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function renderTable(rows) {
            const tbody = document.getElementById('providersTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = '';

            if (rows.length === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
                rows.forEach(director => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${escapeHtml(director.nombre)}</td>
                        <td>
                            <a href="https://wa.me/${director.telf}" target="_blank" style="color: var(--azul-anil); text-decoration: none; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                                ${escapeHtml(director.telf)}
                            </a>
                        </td>
                        <td>${escapeHtml(director.ced_dir)}</td>
                        <td>
                            <div class="action-buttons-cell">
                                <button class="btn-edit" onclick="editProvider('${director.ced_dir}')" title="Editar">
                                    <i data-lucide="edit-3" class="btn-icon"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteProvider('${director.ced_dir}')" title="Eliminar">
                                    <i data-lucide="trash-2" class="btn-icon"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        }

        function renderPaginationControls(totalPages, start, end, total) {
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationNumbers = document.getElementById('paginationNumbers');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');

            if (total === 0) {
                paginationInfo.textContent = 'No hay registros';
            } else {
                paginationInfo.textContent = `Mostrando ${start + 1} a ${end} de ${total} directores`;
            }

            // Generar números de página
            paginationNumbers.innerHTML = '';
            if (totalPages > 1) {
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.className = `pagination-number ${i === currentPage ? 'active' : ''}`;
                    btn.textContent = i;
                    btn.onclick = () => { currentPage = i; updatePagination(); };
                    paginationNumbers.appendChild(btn);
                }
            }

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        }

        function changePage(direction) {
            currentPage += direction;
            updatePagination();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ===== Modal Logic =====
        function openModal(mode, providerCed = null) {
            const modal = document.getElementById('providerModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('providerForm');
            
            form.reset();
            
            if (mode === 'add') {
                modalTitle.innerHTML = '<i data-lucide="user-plus" class="modal-icon"></i> Nuevo Director';
                document.getElementById('providerCed').value = '';
            } else if (mode === 'edit' && providerCed) {
                modalTitle.innerHTML = '<i data-lucide="edit-3" class="modal-icon"></i> Editar Director';
                
                const director = directors.find(d => d.ced_dir === providerCed);
                if (director) {
                    document.getElementById('providerCed').value = director.ced_dir;
                    document.getElementById('dir_nombre').value = director.nombre;
                    document.getElementById('dir_telf').value = director.telf;
                    document.getElementById('cedula').value = director.ced_dir;
                }
            }
            
            modal.style.display = 'flex';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function closeModal() {
            document.getElementById('providerModal').style.display = 'none';
        }

        function editProvider(cedula) {
            openModal('edit', cedula);
        }

        window.onclick = function(event) {
            const modal = document.getElementById('providerModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // ===== CRUD Operations (Manteniendo lógica original) =====
        async function saveProvider(event) {
            event.preventDefault();
            
            const formData = new FormData();
            const providerCed = document.getElementById('providerCed').value;
            const isEdit = !!providerCed;
            
            formData.append('dir_nombre', document.getElementById('dir_nombre').value);
            formData.append('dir_telf', document.getElementById('dir_telf').value);
            formData.append('cedula', document.getElementById('cedula').value);
            if (isEdit) {
                formData.append('ced_original', providerCed);
            }
            
            try {
                let response;
                const endpoint = isEdit ? '?action=oficinas&method=updateDirector' : '?action=oficinas&method=capturarDirector';
                
                response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: isEdit ? 'Director actualizado correctamente' : 'Director agregado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeModal();
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    throw new Error(result.message || 'Error al guardar');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error inesperado'
                });
            }
        }

        async function deleteProvider(cedula) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E44336',
                cancelButtonColor: '#5F6368',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`?action=oficinas&method=eliminarDirector&cedula=${encodeURIComponent(cedula)}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `cedula=${encodeURIComponent(cedula)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire('Eliminado', 'Director eliminado correctamente', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire('Error', error.message || 'No se pudo eliminar', 'error');
                }
            }
        }
    </script>
</body>
</html>