<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de Inventario - UPEL">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?? 'Inicio' ?> - <?= $titulo ?></title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/inventario.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php'; ?>
        
        <main class="main-content">
            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <i data-lucide="package" class="header-icon"></i>
                    <div>
                        <h1><?= $titulo ?></h1>
                        <p class="header-date">
                            <i data-lucide="calendar" class="date-icon"></i>
                            <span>Gestión de productos del inventario</span>
                        </p>
                    </div>
                </div>
            </header>

            <?php if (isset($_GET['exito'])): ?>
                <div class="alert-box alert-success">
                    <i data-lucide="check-circle" class="alert-icon"></i>
                    <div class="alert-content">
                        <strong>¡Éxito!</strong>
                        <p>Tu producto fue agregado exitosamente</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Sección Principal -->
            <section class="config-section">
                <!-- Barra de búsqueda y acciones -->
                <div class="search-actions-bar">
                    <div class="search-box">
                        <i data-lucide="search" class="search-icon"></i>
                        <input 
                            type="text" 
                            id="buscar" 
                            name="buscar" 
                            class="search-input"
                            placeholder="Buscar por nombre del producto..."
                            onkeyup="filtrarProductos()">
                    </div>
                    <div class="action-buttons">
                        <button class="btn-action btn-primary" id="add">
                            <i data-lucide="plus" class="btn-icon"></i>
                            <span>Agregar Producto</span>
                        </button>
                        <button class="btn-action btn-secondary" id="importar">
                            <i data-lucide="file-down" class="btn-icon"></i>
                            <span>Exportar PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Pagination Controls Top -->
                <div class="pagination-controls-top">
                    <div class="entries-selector">
                        <label for="entriesPerPage">Mostrar:</label>
                        <select id="entriesPerPage" class="entries-select">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="entries-label">productos</span>
                    </div>
                    <div class="pagination-info">
                        <span id="paginationInfo">Mostrando 1 a 10 de 0 productos</span>
                    </div>
                </div>

                <!-- Tabla de Productos -->
                <div class="table-container">
                    <table class="users-table" id="tablaProductos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Presentación</th>
                                <th>Último Registro</th>
                                <th>Tipo</th>
                                <th>Unidades Disponibles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productosTableBody">
                            <?php if(!empty($datosInven)): ?>
                                <?php foreach($datosInven as $dato): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dato['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['medida']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['fecha_r'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($dato['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['un_disponibles'] ?? '0'); ?></td>
                                        <td>
                                            <div class="action-buttons-cell">
                                                <button class="btn-edit btn-editar" 
                                                    data-id="<?php echo $dato['id_producto']; ?>"
                                                    title="Editar producto">
                                                    <i data-lucide="edit-3" class="btn-icon"></i>
                                                </button>
                                                <button class="btn-delete btn-eliminar"
                                                    data-id="<?php echo $dato['id_producto']; ?>"
                                                    title="Eliminar producto">
                                                    <i data-lucide="trash-2" class="btn-icon"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="no-data">
                                        <i data-lucide="package-x" class="no-data-icon"></i>
                                        <p>No hay productos en inventario</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Navigation -->
                <div class="pagination-controls-bottom">
                    <nav class="pagination-nav" aria-label="Paginación de productos">
                        <button class="pagination-btn" id="prevPage" aria-label="Página anterior">
                            <i data-lucide="chevron-left" class="pagination-icon"></i>
                        </button>
                        <div class="pagination-numbers" id="paginationNumbers"></div>
                        <button class="pagination-btn" id="nextPage" aria-label="Página siguiente">
                            <i data-lucide="chevron-right" class="pagination-icon"></i>
                        </button>
                    </nav>
                </div>
            </section>

            <!-- Modal de agregar producto -->
            <div id="productModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>
                            <i data-lucide="package-plus" class="modal-icon"></i>
                            Agregar Nuevo Producto
                        </h2>
                        <button class="modal-close" id="closeAddModal">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <form id="productForm" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="productName" class="form-label">
                                    <i data-lucide="tag" class="label-icon"></i>
                                    <span>Nombre del Producto<span class="required">*</span></span>
                                </label>
                                <div class="input-wrapper">
                                    <i data-lucide="box" class="input-icon"></i>
                                    <input 
                                        type="text" 
                                        id="productName" 
                                        name="productName"
                                        class="form-input"
                                        placeholder="Ej: Papel bond tamaño carta"
                                        required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="productMeasure" class="form-label">
                                    <i data-lucide="ruler" class="label-icon"></i>
                                    <span>Unidad de Medida<span class="required">*</span></span>
                                </label>
                                <div class="select-wrapper">
                                    <i data-lucide="package" class="select-icon"></i>
                                    <select id="productMeasure" name="productMeasure" class="form-select" required>
                                        <option value="">Seleccione una unidad...</option>
                                        <option value="Unidades">Unidades</option>
                                        <option value="Kilogramos">Kilogramos</option>
                                        <option value="Litros">Litros</option>
                                        <option value="Cajas">Cajas</option>
                                        <option value="Paquetes">Paquetes</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    <i data-lucide="chevron-down" class="select-arrow"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipo_p" class="form-label">
                                    <i data-lucide="folder" class="label-icon"></i>
                                    <span>Tipo de Producto<span class="required">*</span></span>
                                </label>
                                <div class="select-wrapper">
                                    <i data-lucide="layers" class="select-icon"></i>
                                    <select name="tipo_p" id="tipo_p" class="form-select" required>
                                        <?php if(isset($tipos_p['success']) && $tipos_p['success'] && !empty($tipos_p['data'])): ?>
                                            <?php foreach($tipos_p['data'] as $tipo): ?>
                                                <option value="<?php echo $tipo['id_tipo']?>"><?php echo $tipo['nombre']?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="Alimento" selected>Alimento</option>
                                            <option value="Limpieza">Limpieza</option>
                                            <option value="Electronicos">Electrónicos</option>
                                            <option value="Oficina">Oficina</option>
                                            <option value="Material literario">Material literario</option>
                                        <?php endif; ?>
                                    </select>
                                    <i data-lucide="chevron-down" class="select-arrow"></i>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn-submit" name="add">
                                <i data-lucide="check" class="btn-icon"></i>
                                <span>Guardar Producto</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de editar producto -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>
                            <i data-lucide="edit" class="modal-icon"></i>
                            Editar Producto
                        </h2>
                        <button class="modal-close" id="closeEditModal">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <form id="editForm" method="post" action="?action=inventario&method=actualizarProducto">
                        <input type="hidden" id="editId" name="editId">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="editName" class="form-label">
                                    <i data-lucide="tag" class="label-icon"></i>
                                    <span>Nombre del Producto<span class="required">*</span></span>
                                </label>
                                <div class="input-wrapper">
                                    <i data-lucide="box" class="input-icon"></i>
                                    <input 
                                        type="text" 
                                        id="editName" 
                                        name="nombre"
                                        class="form-input"
                                        required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="editMeasure" class="form-label">
                                    <i data-lucide="ruler" class="label-icon"></i>
                                    <span>Unidad de Medida<span class="required">*</span></span>
                                </label>
                                <div class="select-wrapper">
                                    <i data-lucide="package" class="select-icon"></i>
                                    <select id="editMeasure" name="editMeasure" class="form-select" required>
                                        <option value="">Seleccione una unidad...</option>
                                        <option value="Unidades">Unidades</option>
                                        <option value="Kilogramos">Kilogramos</option>
                                        <option value="Litros">Litros</option>
                                        <option value="Cajas">Cajas</option>
                                        <option value="Paquetes">Paquetes</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    <i data-lucide="chevron-down" class="select-arrow"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="editTipo" class="form-label">
                                    <i data-lucide="folder" class="label-icon"></i>
                                    <span>Tipo de Producto<span class="required">*</span></span>
                                </label>
                                <div class="select-wrapper">
                                    <i data-lucide="layers" class="select-icon"></i>
                                    <select name="editTipo" id="editTipo" class="form-select" required>
                                        <?php if(isset($tipos_p['success']) && $tipos_p['success'] && !empty($tipos_p['data'])): ?>
                                            <?php foreach($tipos_p['data'] as $tipo): ?>
                                                <option value="<?php echo $tipo['id_tipo']?>"><?php echo $tipo['nombre']?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="Alimento" selected>Alimento</option>
                                            <option value="Limpieza">Limpieza</option>
                                            <option value="Electronicos">Electrónicos</option>
                                            <option value="Oficina">Oficina</option>
                                            <option value="Material literario">Material literario</option>
                                        <?php endif; ?>
                                    </select>
                                    <i data-lucide="chevron-down" class="select-arrow"></i>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn-submit">
                                <i data-lucide="save" class="btn-icon"></i>
                                <span>Actualizar Producto</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Inicializar Lucide Icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // ===== Paginación de Productos =====
            const allRows = Array.from(document.querySelectorAll('#productosTableBody tr'));
            let currentPage = 1;
            let entriesPerPage = 10;
            
            function updatePagination() {
                const totalEntries = allRows.length;
                const totalPages = Math.ceil(totalEntries / entriesPerPage);
                
                allRows.forEach(row => row.style.display = 'none');
                
                const startIndex = (currentPage - 1) * entriesPerPage;
                const endIndex = Math.min(startIndex + entriesPerPage, totalEntries);
                
                for (let i = startIndex; i < endIndex; i++) {
                    if (allRows[i]) {
                        allRows[i].style.display = '';
                    }
                }
                
                const paginationInfo = document.getElementById('paginationInfo');
                if (totalEntries === 0) {
                    paginationInfo.textContent = 'No hay productos para mostrar';
                } else {
                    paginationInfo.textContent = `Mostrando ${startIndex + 1} a ${endIndex} de ${totalEntries} productos`;
                }
                
                generatePageNumbers(totalPages);
                
                const prevBtn = document.getElementById('prevPage');
                const nextBtn = document.getElementById('nextPage');
                
                if (prevBtn && nextBtn) {
                    prevBtn.disabled = currentPage === 1;
                    nextBtn.disabled = currentPage === totalPages || totalPages === 0;
                }
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
            
            function generatePageNumbers(totalPages) {
                const paginationNumbers = document.getElementById('paginationNumbers');
                if (!paginationNumbers) return;
                
                paginationNumbers.innerHTML = '';
                
                if (totalPages <= 1) return;
                
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
                
                if (endPage - startPage < maxVisiblePages - 1) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }
                
                if (startPage > 1) {
                    paginationNumbers.appendChild(createPageButton(1));
                    if (startPage > 2) {
                        const dots = document.createElement('span');
                        dots.className = 'pagination-dots';
                        dots.textContent = '...';
                        paginationNumbers.appendChild(dots);
                    }
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationNumbers.appendChild(createPageButton(i));
                }
                
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const dots = document.createElement('span');
                        dots.className = 'pagination-dots';
                        dots.textContent = '...';
                        paginationNumbers.appendChild(dots);
                    }
                    paginationNumbers.appendChild(createPageButton(totalPages));
                }
            }
            
            function createPageButton(pageNum) {
                const button = document.createElement('button');
                button.className = 'pagination-number';
                button.textContent = pageNum;
                button.setAttribute('aria-label', `Página ${pageNum}`);
                
                if (pageNum === currentPage) {
                    button.classList.add('active');
                    button.setAttribute('aria-current', 'page');
                }
                
                button.addEventListener('click', () => {
                    currentPage = pageNum;
                    updatePagination();
                });
                
                return button;
            }
            
            const entriesSelect = document.getElementById('entriesPerPage');
            if (entriesSelect) {
                entriesSelect.addEventListener('change', function() {
                    entriesPerPage = parseInt(this.value);
                    currentPage = 1;
                    updatePagination();
                });
            }
            
            const prevBtn = document.getElementById('prevPage');
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });
            }
            
            const nextBtn = document.getElementById('nextPage');
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const totalPages = Math.ceil(allRows.length / entriesPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });
            }
            
            updatePagination();
        });

        // ===== Modales =====
        const modalAgregar = document.getElementById("productModal");
        const btnAgregar = document.getElementById("add");
        const btnCerrarAgregar = document.getElementById("closeAddModal");
        const formAgregar = document.getElementById("productForm");

        btnAgregar.addEventListener("click", function() {
            modalAgregar.style.display = "flex";
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        btnCerrarAgregar.addEventListener("click", function() {
            modalAgregar.style.display = "none";
            formAgregar.reset();
        });

        window.addEventListener("click", function(event) {
            if (event.target === modalAgregar) {
                modalAgregar.style.display = "none";
                formAgregar.reset();
            }
        });

        // Modal editar
        const modalEditar = document.getElementById('editModal');
        const btnCerrarEditar = document.getElementById('closeEditModal');
        const formEditar = document.getElementById('editForm');

        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                const id = btn.getAttribute('data-id');
                fetch(`?action=inventario&method=obtenerProducto&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const producto = data.producto;
                            document.getElementById('editId').value = producto.id_producto;
                            document.getElementById('editName').value = producto.nombre;
                            document.getElementById('editMeasure').value = producto.medida;
                            document.getElementById('editTipo').value = producto.id_tipo;
                            modalEditar.style.display = 'flex';
                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo cargar el producto', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'No se pudo cargar el producto', 'error');
                    });
            }
        });

        btnCerrarEditar.addEventListener('click', function() {
            modalEditar.style.display = 'none';
            formEditar.reset();
        });

        window.addEventListener('click', function(e) {
            if (e.target === modalEditar) {
                modalEditar.style.display = 'none';
                formEditar.reset();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (modalEditar.style.display === 'flex') {
                    modalEditar.style.display = 'none';
                    formEditar.reset();
                }
                if (modalAgregar.style.display === 'flex') {
                    modalAgregar.style.display = 'none';
                    formAgregar.reset();
                }
            }
        });

        // ===== Eliminar =====
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', function (e) {
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
                            window.location.href = `?action=inventario&method=eliminarProducto&id=${id}`;
                        }
                    });
                });
            });
        });

        // ===== Búsqueda =====
        function filtrarProductos() {
            let input = document.getElementById('buscar');
            let filtro = input.value.toUpperCase();
            let tabla = document.getElementById('tablaProductos');
            let filas = tabla.getElementsByTagName('tr');
            
            for (let i = 1; i < filas.length; i++) {
                let mostrarFila = false;
                let nombre = filas[i].getElementsByTagName('td')[0];
                
                if (nombre) {
                    let textoNombre = nombre.textContent || nombre.innerText;
                    
                    if (textoNombre.toUpperCase().indexOf(filtro) > -1) {
                        mostrarFila = true;
                    }
                }
                
                filas[i].style.display = mostrarFila ? '' : 'none';
            }
        }

        // ===== Exportar PDF =====
        document.getElementById('importar').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            
            const title = "Reporte del Inventario";
            doc.setFontSize(18);
            doc.text(title, 40, 40);
            
            const table = document.getElementById('tablaProductos');
            const headers = [];
            const rows = [];
            
            table.querySelectorAll('thead th').forEach((th, index) => {
                if(index < 5) {
                    headers.push(th.textContent);
                }
            });
            
            table.querySelectorAll('tbody tr').forEach(tr => {
                if(tr.querySelector('td[colspan]')) return;
                
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    if(index < 5) {
                        row.push(td.textContent.trim());
                    }
                });
                if(row.length > 0) rows.push(row);
            });

            const options = {
                startY: 60,
                head: [headers],
                body: rows,
                theme: 'grid',
                headStyles: {
                    fillColor: [63, 81, 181],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                didDrawPage: function (data) {
                    doc.setFontSize(10);
                    const pageCount = doc.internal.getNumberOfPages();
                    doc.text(`Página ${data.pageNumber} de ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 20);
                    
                    const fecha = new Date().toLocaleDateString();
                    doc.text(`Generado el: ${fecha}`, doc.internal.pageSize.width - 120, doc.internal.pageSize.height - 20);
                }
            };
            
            doc.autoTable(options);
            doc.save('Reporte_Inventario.pdf');
        });
    </script>
</body>
</html>