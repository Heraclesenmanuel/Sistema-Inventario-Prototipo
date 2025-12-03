<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Inicio' ?> - <?= $titulo ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/oficinas.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">


</head>
<body>
    <div class="dashboard">
        <?= include_once 'views/inc/heder.php'; ?>
        <main class="main-content">
            <div class="page-header">
                <h2><?= $titulo ?></h2>
                <h4>Hoy es: <?= APP_Date ?> </h4>
            </div>

            <div class="add">
                <form action="?action=oficinas&method=home" method="post" id="oficina-form">
                    <h3>Agregar Nueva Oficina</h3>
                    <?php if (isset($_GET['exito'])): ?>
                        <div class="alert alert-success">
                            <i class="fa fa-calendar-check"></i>
                            <span>...¡Tu solicitud fue agregada exitosamente!</span>
                            <i class="fa fa-long-arrow-down"></i>
                        </div>
                    <?php endif; ?>
                    <!-- Datos de la oficina -->
                    <h4 class="form-section-title">Datos de la Oficina</h4>
                    <input type="number" id="num_oficina" name="num_oficina" placeholder="Ingrese el número de esta oficina" min=100 max=399 required>
                    <input type="text" id="name" name="name" placeholder="Ingrese el nombre de esta oficina" required>
                    <input type="text" id="cel" name="cel" placeholder="Ingrese el numero de teléfono de oficina" pattern="\d{11,}" required>
                    
                    <!-- Opciones para el director -->
                    <div class="director-options">
                        <button type="button" class="director-btn" id="btn-select-director">
                            <i class="fas fa-user-check"></i> Seleccionar Director Existente
                        </button>
                        <button type="button" class="director-btn" id="btn-new-director">
                            <i class="fas fa-user-plus"></i> Agregar Nuevo Director
                        </button>
                    </div>
                    
                    <!-- Contenedor del select de directores -->
                    <div class="director-form-container" id="select-director-container">
                        <h4 class="form-section-title">Seleccionar Director Existente</h4>
                        <select id="dir_cedula" name="dir_cedula" class="form-select">
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
                        <button type="button" id="btn-dir" name="btn-dir"
                        onclick="window.location.href='?action=oficinas&method=directores'">
                            <i class="fas fa-plus"></i> Ver directores
                        </button>
                    </div>
                    <!-- Formulario de datos del director -->
                    <div class="director-form-container" id="director-data-container">
                        <h4 class="form-section-title">Datos del Director</h4>
                        <div class="director-form">
                            <input type="text" id="cedula" name="cedula" placeholder="Cédula del director" required readonly>
                            <input type="text" id="dir_nombre" name="dir_nombre" placeholder="Nombre completo del director" required readonly>
                            <input type="text" id="dir_telf" name="dir_telf" placeholder="Teléfono del director" pattern="\d{11,}" required readonly>
                        </div>
                    </div>

                    <!-- Campos ocultos que siempre se enviarán -->
                    <input type="hidden" id="hidden_cedula" name="cedula" value="">
                    <input type="hidden" id="hidden_dir_nombre" name="dir_nombre" value="">
                    <input type="hidden" id="hidden_dir_telf" name="dir_telf" value="">
                    <input type="hidden" id="modo_director" name="modo_director" value="">
                    
                    <button type="submit" id="btn-add" name="btn-add">
                        <i class="fas fa-plus"></i> Agregar Oficina
                    </button>
                </form>
            </div>
                    
            <!-- El resto de tu código permanece igual -->
            <div class="viewsUser">
                <h3>Oficinas Registradas</h3>
                
                <div class="table-controls">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="buscar" placeholder="Buscar por nombre de la oficina, cédula o teléfono del director...">
                    </div>
                    <div class="entries-control">
                        <label for="entries-select">Mostrar:</label>
                        <select id="entries-select">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="all">Todos</option>
                        </select>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table id="tabla-clientes">
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
                                            <div class="btn-group">
                                                <button
                                                    class="btn btn-see view" 
                                                    data-action="view"
                                                    title="Ver más" 
                                                    data-id="<?php echo $oficina['num_oficina']; ?>"
                                                    data-oficina='<?php echo json_encode($oficina); ?>'>
                                                    <i class="fas fa-eye"></i>
                                                    <span>Más</span>
                                                </button>
                                                <button 
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-action="erase"
                                                    title="Eliminar oficina" 
                                                    data-id="<?php echo $oficina['num_oficina']; ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x"></i>
                                            <h5>No hay Oficinas registradas</h5>
                                            <p>¡Comienza agregando tu primera oficina!</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    <div class="pagination-info" id="pagination-info"></div>
                    <div class="pagination" id="pagination"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para ver detalles de la oficina -->
    <div class="modal-overlay" id="officeModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-building"></i> Detalles de la Oficina</h3>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="office-info">
                    <div class="info-section">
                        <h4><i class="fas fa-info-circle"></i> Información General</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Número de Oficina</span>
                                <span class="info-value" id="modal-num-oficina">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nombre</span>
                                <span class="info-value" id="modal-nombre">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Teléfono</span>
                                <span class="info-value" id="modal-telefono">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h4><i class="fas fa-user-tie"></i> Información del Director</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nombre</span>
                                <span class="info-value" id="modal-director-nombre">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Cédula</span>
                                <span class="info-value" id="modal-director-cedula">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Teléfono</span>
                                <span class="info-value" id="modal-director-telefono">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h4><i class="fas fa-calendar-alt"></i> Información Adicional</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Fecha de Creación</span>
                                <span class="info-value" id="modal-fecha-creacion"><?php echo date('d/m/Y'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Estado</span>
                                <span class="info-value" id="modal-estado">Activa</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total Empleados</span>
                                <span class="info-value" id="modal-total-empleados">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del modal
            const officeModal = document.getElementById('officeModal');
            const closeModal = document.getElementById('closeModal');
            
            // Elementos del formulario de director (mantener tu código existente)
            const btnSelectDirector = document.getElementById('btn-select-director');
            const btnNewDirector = document.getElementById('btn-new-director');
            const selectDirectorContainer = document.getElementById('select-director-container');
            const directorDataContainer = document.getElementById('director-data-container');
            const directorSelect = document.getElementById('dir_cedula');
            const dirCedula = document.getElementById('cedula');
            const dirNombre = document.getElementById('dir_nombre');
            const dirTelefono = document.getElementById('dir_telf');
            const hiddenCedula = document.getElementById('hidden_cedula');
            const hiddenDirNombre = document.getElementById('hidden_dir_nombre');
            const hiddenDirTelf = document.getElementById('hidden_dir_telf');
            const modoDirectorInput = document.getElementById('modo_director');
            const oficinaForm = document.getElementById('oficina-form');

            let modoDirector = ''; // 'existente' o 'nuevo'

            // Función para abrir el modal con datos
            function openOfficeModal(oficinaData) {
                try {
                    // Parsear datos si viene como string JSON
                    if (typeof oficinaData === 'string') {
                        oficinaData = JSON.parse(oficinaData);
                    }
                    
                    // Actualizar contenido del modal
                    document.getElementById('modal-num-oficina').textContent = oficinaData.num_oficina || '-';
                    document.getElementById('modal-nombre').textContent = oficinaData.nombre || '-';
                    document.getElementById('modal-telefono').textContent = oficinaData.telefono || '-';
                    document.getElementById('modal-director-nombre').textContent = oficinaData.nombre_dir || '-';
                    document.getElementById('modal-director-cedula').textContent = oficinaData.ced_dir || '-';
                    document.getElementById('modal-director-telefono').textContent = oficinaData.telf_dir || '-';
                    
                    // Datos de prueba adicionales
                    document.getElementById('modal-fecha-creacion').textContent = '<?php echo date("d/m/Y"); ?>';
                    document.getElementById('modal-estado').textContent = 'Activa';
                    document.getElementById('modal-total-empleados').textContent = Math.floor(Math.random() * 10) + 1; // Número aleatorio para ejemplo
                    
                    // Mostrar modal
                    officeModal.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevenir scroll del body
                    
                } catch (error) {
                    console.error('Error al cargar datos de la oficina:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar los datos de la oficina.',
                        icon: 'error',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            }

            // Cerrar modal
            function closeOfficeModal() {
                officeModal.classList.remove('active');
                document.body.style.overflow = 'auto'; // Restaurar scroll del body
            }

            // Evento para cerrar modal con botón X
            closeModal.addEventListener('click', closeOfficeModal);

            // Evento para cerrar modal haciendo clic fuera
            officeModal.addEventListener('click', function(e) {
                if (e.target === officeModal) {
                    closeOfficeModal();
                }
            });

            // Evento para cerrar modal con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && officeModal.classList.contains('active')) {
                    closeOfficeModal();
                }
            });

            // Función para actualizar campos ocultos
            function actualizarCamposOcultos() {
                hiddenCedula.value = dirCedula.value;
                hiddenDirNombre.value = dirNombre.value;
                hiddenDirTelf.value = dirTelefono.value;
            }

            // Función para resetear el estado
            function resetDirectorForm() {
                selectDirectorContainer.classList.remove('active');
                directorDataContainer.classList.remove('active');
                btnSelectDirector.classList.remove('active');
                btnNewDirector.classList.remove('active');
                directorSelect.value = '';
                dirCedula.value = '';
                dirNombre.value = '';
                dirTelefono.value = '';
                hiddenCedula.value = '';
                hiddenDirNombre.value = '';
                hiddenDirTelf.value = '';
                modoDirectorInput.value = '';
                
                // Hacer campos de solo lectura en lugar de deshabilitados
                dirCedula.readOnly = true;
                dirNombre.readOnly = true;
                dirTelefono.readOnly = true;
                
                modoDirector = '';
            }

            // Seleccionar director existente
            btnSelectDirector.addEventListener('click', function() {
                resetDirectorForm();
                modoDirector = 'existente';
                modoDirectorInput.value = 'existente';
                this.classList.add('active');
                selectDirectorContainer.classList.add('active');
                directorDataContainer.classList.add('active');
                
                // Campos de solo lectura (no deshabilitados)
                dirCedula.readOnly = true;
                dirNombre.readOnly = true;
                dirTelefono.readOnly = true;
            });

            // Agregar nuevo director
            btnNewDirector.addEventListener('click', function() {
                resetDirectorForm();
                modoDirector = 'nuevo';
                modoDirectorInput.value = 'nuevo';
                this.classList.add('active');
                directorDataContainer.classList.add('active');
                
                // Campos editables
                dirCedula.readOnly = false;
                dirNombre.readOnly = false;
                dirTelefono.readOnly = false;
                
                // Limpiar campos
                dirCedula.value = '';
                dirNombre.value = '';
                dirTelefono.value = '';
            });

            // Cuando se selecciona un director del dropdown
            directorSelect.addEventListener('change', function() {
                if (this.value && modoDirector === 'existente') {
                    const selectedOption = this.options[this.selectedIndex];
                    dirCedula.value = this.value;
                    dirNombre.value = selectedOption.getAttribute('data-nombre');
                    dirTelefono.value = selectedOption.getAttribute('data-telf');
                    
                    // Actualizar campos ocultos
                    actualizarCamposOcultos();
                }
            });

            // Actualizar campos ocultos cuando cambien los inputs visibles
            dirCedula.addEventListener('input', actualizarCamposOcultos);
            dirNombre.addEventListener('input', actualizarCamposOcultos);
            dirTelefono.addEventListener('input', actualizarCamposOcultos);

            // Validación del formulario antes de enviar
            oficinaForm.addEventListener('submit', function(e) {
                // Asegurar que los campos ocultos estén actualizados
                actualizarCamposOcultos();

                if (!modoDirector) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Seleccione una opción',
                        text: 'Por favor, seleccione si desea usar un director existente o agregar uno nuevo.',
                        icon: 'warning',
                        confirmButtonColor: '#3498db'
                    });
                    return;
                }

                if (modoDirector === 'existente' && !directorSelect.value) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Director no seleccionado',
                        text: 'Por favor, seleccione un director de la lista.',
                        icon: 'warning',
                        confirmButtonColor: '#3498db'
                    });
                    return;
                }

                if (modoDirector === 'nuevo' && (!dirCedula.value || !dirNombre.value || !dirTelefono.value)) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Datos incompletos',
                        text: 'Por favor, complete todos los datos del nuevo director.',
                        icon: 'warning',
                        confirmButtonColor: '#3498db'
                    });
                    return;
                }

                // Si todo está bien, el formulario se envía normalmente
                // Los campos ocultos aseguran que los datos lleguen al servidor
            });

            // El resto de tu código de búsqueda y paginación permanece igual
            const buscarInput = document.getElementById('buscar');
            const entriesSelect = document.getElementById('entries-select');
            const tabla = document.getElementById('tabla-clientes');
            const tbody = tabla.querySelector('tbody');
            const paginationInfo = document.getElementById('pagination-info');
            const paginationContainer = document.getElementById('pagination');
            
            let todasLasFilas = Array.from(tbody.querySelectorAll('tr')).filter(tr => 
                !tr.querySelector('.text-muted')
            );
            
            let filasFiltradas = [...todasLasFilas];
            let paginaActual = 1;
            let filasPorPagina = 10;

            function filtrarClientes() {
                const textoBusqueda = buscarInput.value.toLowerCase().trim();
                
                if (textoBusqueda === '') {
                    filasFiltradas = [...todasLasFilas];
                } else {
                    filasFiltradas = todasLasFilas.filter(fila => {
                        const celdas = fila.querySelectorAll('td:not(:last-child)');
                        return Array.from(celdas).some(celda => 
                            celda.textContent.toLowerCase().includes(textoBusqueda)
                        );
                    });
                }
                
                paginaActual = 1;
                mostrarPagina();
            }

            function mostrarPagina() {
                // Limpiar tbody
                tbody.innerHTML = '';
                
                // Verificar si hay filas para mostrar
                if (filasFiltradas.length === 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td colspan="5">
                            <div class="text-muted">
                                <i class="fas fa-search fa-3x"></i>
                                <h5>No se encontraron resultados</h5>
                                <p>No hay oficinas que coincidan con tu búsqueda.</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                    paginationInfo.textContent = '';
                    paginationContainer.innerHTML = '';
                    return;
                }
                
                // Calcular paginación
                const totalFilas = filasFiltradas.length;
                const totalPaginas = filasPorPagina === 'all' ? 1 : Math.ceil(totalFilas / filasPorPagina);
                
                // Ajustar página actual si es necesaria
                if (paginaActual > totalPaginas) {
                    paginaActual = totalPaginas;
                }
                
                // Calcular índices
                let inicio, fin;
                if (filasPorPagina === 'all') {
                    inicio = 0;
                    fin = totalFilas;
                } else {
                    inicio = (paginaActual - 1) * filasPorPagina;
                    fin = Math.min(inicio + filasPorPagina, totalFilas);
                }
                
                // Mostrar filas de la página actual
                for (let i = inicio; i < fin; i++) {
                    tbody.appendChild(filasFiltradas[i].cloneNode(true));
                }
                
                // Reactivar eventos de eliminar y ver
                activarBotonesEliminar();
                activarBotonesVer();
                
                // Actualizar información de paginación
                paginationInfo.textContent = `Mostrando ${inicio + 1} a ${fin} de ${totalFilas} oficinas`;
                
                // Generar botones de paginación
                generarPaginacion(totalPaginas);
            }

            function activarBotonesVer() {
                tbody.querySelectorAll('.btn.btn-see.view').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const oficinaData = this.getAttribute('data-oficina');
                        openOfficeModal(oficinaData);
                    });
                });
            }

            function generarPaginacion(totalPaginas) {
                paginationContainer.innerHTML = '';
                
                if (totalPaginas <= 1) return;
                
                // Botón anterior
                const btnPrev = document.createElement('button');
                btnPrev.innerHTML = '<i class="fas fa-chevron-left"></i>';
                btnPrev.disabled = paginaActual === 1;
                btnPrev.addEventListener('click', () => {
                    if (paginaActual > 1) {
                        paginaActual--;
                        mostrarPagina();
                    }
                });
                paginationContainer.appendChild(btnPrev);
                
                // Botones de páginas
                const maxBotones = 5;
                let inicioPagina = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
                let finPagina = Math.min(totalPaginas, inicioPagina + maxBotones - 1);
                
                if (finPagina - inicioPagina < maxBotones - 1) {
                    inicioPagina = Math.max(1, finPagina - maxBotones + 1);
                }
                
                if (inicioPagina > 1) {
                    const btn1 = document.createElement('button');
                    btn1.textContent = '1';
                    btn1.addEventListener('click', () => {
                        paginaActual = 1;
                        mostrarPagina();
                    });
                    paginationContainer.appendChild(btn1);
                    
                    if (inicioPagina > 2) {
                        const btnDots = document.createElement('button');
                        btnDots.textContent = '...';
                        btnDots.disabled = true;
                        paginationContainer.appendChild(btnDots);
                    }
                }
                
                for (let i = inicioPagina; i <= finPagina; i++) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    if (i === paginaActual) {
                        btn.classList.add('active');
                    }
                    btn.addEventListener('click', () => {
                        paginaActual = i;
                        mostrarPagina();
                    });
                    paginationContainer.appendChild(btn);
                }
                
                if (finPagina < totalPaginas) {
                    if (finPagina < totalPaginas - 1) {
                        const btnDots = document.createElement('button');
                        btnDots.textContent = '...';
                        btnDots.disabled = true;
                        paginationContainer.appendChild(btnDots);
                    }
                    
                    const btnLast = document.createElement('button');
                    btnLast.textContent = totalPaginas;
                    btnLast.addEventListener('click', () => {
                        paginaActual = totalPaginas;
                        mostrarPagina();
                    });
                    paginationContainer.appendChild(btnLast);
                }
                
                // Botón siguiente
                const btnNext = document.createElement('button');
                btnNext.innerHTML = '<i class="fas fa-chevron-right"></i>';
                btnNext.disabled = paginaActual === totalPaginas;
                btnNext.addEventListener('click', () => {
                    if (paginaActual < totalPaginas) {
                        paginaActual++;
                        mostrarPagina();
                    }
                });
                paginationContainer.appendChild(btnNext);
            }

            function activarBotonesEliminar() {
                tbody.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Esta acción no se puede deshacer.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e74c3c',
                            cancelButtonColor: '#95a5a6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Realizar la eliminación por AJAX
                                fetch(`?action=oficinas&method=deleteOficina&id=${id}`)
                                    .then(response => {
                                        if (!response.ok) throw new Error("Error en la respuesta");
                                        return response.json(); // leer como JSON
                                    })
                                    .then(data => {
                                        if (data.success) {
                                        Swal.fire({
                                            title: 'Eliminado',
                                            text: 'La oficina se eliminó con éxito.',
                                            icon: 'success',
                                            confirmButtonColor: '#3498db'
                                        }).then(() => {
                                            // Puedes recargar o eliminar la fila directamente
                                            window.location.reload();
                                        });
                                        } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: data.error || 'No se pudo eliminar la oficina.',
                                            icon: 'error',
                                            confirmButtonColor: '#e74c3c'
                                        });
                                        }
                                    })
                                    .catch(() => {
                                        Swal.fire({
                                        title: 'Error',
                                        text: 'No se pudo conectar con el servidor.',
                                        icon: 'error',
                                        confirmButtonColor: '#e74c3c'
                                        });
                                    });

                            }
                        });
                    });
                });
            }

            // Debounce para búsqueda
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            // Event listeners
            buscarInput.addEventListener('input', debounce(filtrarClientes, 300));
            
            entriesSelect.addEventListener('change', function() {
                filasPorPagina = this.value === 'all' ? 'all' : parseInt(this.value);
                paginaActual = 1;
                mostrarPagina();
            });

            // Inicializar
            mostrarPagina();
            activarBotonesVer(); // Activar botones de ver en la carga inicial
        });
    </script>
</body>
</html>