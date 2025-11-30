<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'APP' ?> - Proveedores</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/proveedores.css">
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <h2>Gestión de Proveedores</h2>
                    <p class="subtitle">Administra y organiza tu red de proveedores</p>
                </div>
                <button class="btn-primary" onclick="openModal('add')">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Nuevo Proveedor
                </button>
            </div>

            <div class="search-filter-bar">
                <div class="search-box">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Buscar proveedores..." onkeyup="filterProviders()">
                </div>
                
                <div class="filter-group">
                    <select id="statusFilter" onchange="filterProviders()" class="filter-select">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

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
                                    <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
                                    <td><?= htmlspecialchars($proveedor['email']) ?></td>
                                    <td>
                                        <a href="https://wa.me/<?= $proveedor['telefono'] ?>" target="_blank" class="phone-link">
                                            <?= htmlspecialchars($proveedor['telefono']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($proveedor['estado']) ?>">
                                            <?= htmlspecialchars($proveedor['estado']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($proveedor['rif']) ?></td>
                                    <td>
                                        <button class="edit action-btn" onclick="editProvider('<?= $proveedor['rif'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete action-btn" onclick="deleteProvider('<?= $proveedor['rif'] ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay proveedores disponibles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div id="emptyState" class="empty-state" style="display: <?= empty($proveedores) ? 'block' : 'none' ?>;">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                        <path d="M32 8v48M8 32h48" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.3"/>
                        <circle cx="32" cy="32" r="24" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                    </svg>
                    <h3>No hay proveedores</h3>
                    <p>Comienza agregando tu primer proveedor</p>
                    <button class="btn-primary" onclick="openModal('add')">Agregar Proveedor</button>
                </div>
            </div>
        </main>
    </div>

    <div id="providerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Proveedor</h2>
                <button class="close-btn" onclick="closeModal()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            
            <form id="providerForm" method="post" onsubmit="saveProvider(event)">
                <input type="hidden" id="providerRif" name="rif_original">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="providerName">Nombre del Proveedor *</label>
                        <input type="text" id="providerName" name="nombre" required placeholder="Ej: Distribuidora ABC">
                    </div>

                    <div class="form-group">
                        <label for="providerEmail">Email *</label>
                        <input type="email" id="providerEmail" name="email" required placeholder="contacto@proveedor.com">
                    </div>

                    <div class="form-group">
                        <label for="providerPhone">Teléfono *</label>
                        <input type="tel" id="providerPhone" name="telefono" required placeholder="0414 5000000">
                    </div>
                    
                    <div class="form-group">
                        <label for="rif">RIF *</label>
                        <input type="text" id="rif" name="rif" required placeholder="Ej: J-12345678">
                    </div>

                    <div class="form-group">
                        <label for="providerStatus">Estado *</label>
                        <select id="providerStatus" name="estado" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="providerAddress">Dirección</label>
                        <input type="text" id="providerAddress" name="direccion" placeholder="Calle, Ciudad, País">
                    </div>
                    <!-- Nueva sección: Categorías de Especialización -->
                <div class="form-group full-width">
                    <label>Categorías de Especialización</label>
                    <div class="categorias-grid">
                        <?php if(!empty($categorias)): ?>
                            <?php foreach($categorias['data'] as $categoria): ?>
                                <div class="categoria-item">
                                    <input type="checkbox" id="cat_<?php echo $categoria['nombre']?>" name="categorias[]" value=<?php echo $categoria['id_tipo']?>>
                                    <label for="cat_<?php echo $categoria['nombre']?>"><?php echo $categoria['nombre']?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                    <div class="form-group full-width">
                        <label for="providerNotes">Notas</label>
                        <textarea id="providerNotes" name="nota" rows="3" placeholder="Información adicional sobre el proveedor..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let providers = <?= json_encode($proveedores ?? []) ?>;

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Proveedores cargados:', providers);
            updateEmptyState();
        });

        // Filtrar proveedores
        function filterProviders() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            
            const rows = document.querySelectorAll('#providersTableBody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const status = row.dataset.status;
                
                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            updateEmptyState(visibleCount === 0);
        }

        // Actualizar estado vacío
        function updateEmptyState(show = false) {
            const emptyState = document.getElementById('emptyState');
            const table = document.getElementById('providersTable');
            
            if (show || providers.length === 0) {
                emptyState.style.display = 'block';
                table.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                table.style.display = 'table';
            }
        }

        // Editar proveedor
        function editProvider(providerRif) {
            openModal('edit', providerRif);
        }

        // Abrir modal
        function openModal(mode, providerRif = null) {
            const modal = document.getElementById('providerModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('providerForm');
            
            form.reset();
            
            if (mode === 'add') {
                modalTitle.textContent = 'Nuevo Proveedor';
                document.getElementById('providerRif').value = '';
            } else if (mode === 'edit' && providerRif) {
                modalTitle.textContent = 'Editar Proveedor';
                
                // Buscar proveedor por RIF
                const provider = providers.find(p => p.rif === providerRif);
                
                if (provider) {
                    console.log('Proveedor encontrado:', provider);
                    
                    document.getElementById('providerRif').value = provider.rif;
                    document.getElementById('providerName').value = provider.nombre || '';
                    document.getElementById('providerEmail').value = provider.email || '';
                    document.getElementById('providerPhone').value = provider.telefono || '';
                    document.getElementById('providerStatus').value = provider.estado || 'Activo';
                    document.getElementById('providerAddress').value = provider.direccion || '';
                    document.getElementById('providerNotes').value = provider.nota || '';
                    document.getElementById('rif').value = provider.rif || '';
                    // Hacer el campo RIF de solo lectura en edición
                } else {
                    console.error('Proveedor no encontrado con RIF:', providerRif);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información del proveedor'
                    });
                    return;
                }
            }
            
            modal.classList.add('active');
        }

        // Cerrar modal
        function closeModal() {
            const modal = document.getElementById('providerModal');
            modal.classList.remove('active');
        }

        // Guardar proveedor
        async function saveProvider(event) {
            event.preventDefault();
            
            const formData = new FormData();
            const providerRif = document.getElementById('providerRif').value;
            const isEdit = !!providerRif;
            const categoriasSeleccionadas = [];
            const checkboxes = document.querySelectorAll('input[name="categorias[]"]:checked');
            checkboxes.forEach(checkbox => {
                categoriasSeleccionadas.push(checkbox.value);
            });
            
            console.log('Categorías seleccionadas:', categoriasSeleccionadas);
            
            // Agregar datos del formulario
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
                let response;
                if (isEdit) {
                    // Editar proveedor existente\
                    response = await fetch('?action=proveedor&method=updateProveedor', {
                        method: 'POST',
                        body: formData
                    });
                } else {
                    // Agregar nuevo proveedor
                    response = await fetch('?action=proveedor&method=addProveedor', {
                        method: 'POST',
                        body: formData
                    });
                }
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Respuesta no JSON:', text);
                    throw new Error('El servidor no devolvió una respuesta JSON válida');
                }
                const result = await response.json();
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: isEdit ? 'Proveedor actualizado correctamente' : 'Proveedor agregado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeModal();
                    // Recargar la página para ver los cambios
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    throw new Error(result.message || 'Error al guardar el proveedor');
                }
                
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al guardar el proveedor'
                });
            }
        }

        // Eliminar proveedor
        async function deleteProvider(rif) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            
            if (result.isConfirmed) {
                try {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Eliminando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const response = await fetch(`?action=proveedor&method=eliminarProveedor&rif=${encodeURIComponent(rif)}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `rif=${encodeURIComponent(rif)}`
                    });
                    
                    // Obtener el texto de la respuesta primero
                    const responseText = await response.text();
                    console.log('Respuesta del servidor:', responseText);
                    
                    // Intentar parsear como JSON
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (e) {
                        console.error('Error parseando JSON:', e);
                        console.error('Respuesta recibida:', responseText);
                        throw new Error('El servidor no devolvió una respuesta JSON válida. Revisa la consola para más detalles.');
                    }
                    
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'Proveedor eliminado correctamente',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        throw new Error(result.message || 'Error al eliminar el proveedor');
                    }
                    
                } catch (error) {
                    console.error('Error completo:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Error al eliminar el proveedor'
                    });
                }
            }
        }

        // Mostrar notificación
        function showNotification(message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('providerModal')?.addEventListener('click', function(e) {
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

        // Estilos CSS para los badges de estado
        const style = document.createElement('style');
        style.textContent = `
            .status-badge {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: capitalize;
            }
            .status-activo {
                background: #dcfce7;
                color: #166534;
            }
            .status-inactivo {
                background: #fee2e2;
                color: #991b1b;
            }
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                align-items: center;
                justify-content: center;
            }
            .modal.active {
                display: flex;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>