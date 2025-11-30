<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'UPEL' ?> - <?= $titulo ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/inventario.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
<div class="dashboard">
        <?= include_once 'views/inc/heder.php'; ?>
        <main class="main-content">
            <div class="page-header">
                <h1><?= $titulo ?></h1>
            </div>

            <section class="bus-add">
                <div class="bus">
                    <input type="text" id="buscar" name="buscar" placeholder="Ingrese el nombre de la categoria" onkeyup="filtrarProductos()">
                    <button name="add" id="add"><i class="fas fa-plus"></i> Agregar Categoria</button>
                </div>
                <div class="list-Product">
                    <table id="tablaProductos" style="background-color: aliceblue; max-width: 95%;">
                        <thead style="background-color: aqua;">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cantidad por entregar</th>
                                <th>Cantidad solicitada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($categorias['data'])): ?>
                                <?php foreach($categorias['data'] as $categoria): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($categoria['id_tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                                        <td><?php if (!empty($categoria['cant_pend'])): echo htmlspecialchars($categoria['cant_pend']); else: echo 0; endif?></td>
                                        <td><?php if (!empty($categoria['cant_solic'])): echo htmlspecialchars($categoria['cant_solic']); else: echo 0; endif?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-editar" 
                                                data-id="<?php echo $categoria['id_tipo']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                class="btn btn-sm btn-danger btn-eliminar"
                                                title="Eliminar solicitud" 
                                                data-id="<?php echo $categoria['id_tipo']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">
                                        <div class="text-muted">
                                            <i class="fas fa-boxes fa-3x mb-3"></i>
                                            <h5>No hay categorias de inventario</h5>
                                            <p>No se encontraron categorias registradas.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal de agregar categoria -->
                <div id="productModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeAddModal">&times;</span>
                        <h2>Agregar Nueva Categoria</h2>
                        <br>
                        <form id="productForm" method="post">
                            <div class="form-group">
                                <label for="productName">Nombre de la Categoria:</label>
                                <input type="text" id="productName" name="productName" required>
                            </div>
                                <button type="submit" class="submit-btn" id="add" name="add">Guardar Categoria</button>
                            </div>
                            
                        </form>
                    </div>
                </div>

                <!-- Modal de editar producto -->
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeEditModal">&times;</span>
                        <h2>Editar Categoria</h2>
                        <br>
                        <form id="editForm" method="post" action="?action=inventario&method=actualizarCategoria">
                            <input type="hidden" id="editId" name="editId">
                            
                            <div class="form-group">
                                <label for="editName">Nombre de la Categoria:</label>
                                <input type="text" id="editName" name="nombre" required>
                            </div>
                            <button type="submit" class="submit-btn">Actualizar Categoria</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <!-- Funciones del modal de agregar -->
    <script>
        const modalAgregar = document.getElementById("productModal");
        const btnAgregar = document.getElementById("add");
        const btnCerrarAgregar = document.getElementById("closeAddModal");
        const formAgregar = document.getElementById("productForm");

        // Abrir modal de agregar
        btnAgregar.addEventListener("click", function() {
            modalAgregar.style.display = "block";
            calcularPrecioVentaAdd();
        });

        // Cerrar modal de agregar con X
        btnCerrarAgregar.addEventListener("click", function() {
            modalAgregar.style.display = "none";
            formAgregar.reset();
        });

        // Cerrar modal de agregar al hacer clic fuera
        window.addEventListener("click", function(event) {
            if (event.target === modalAgregar) {
                modalAgregar.style.display = "none";
                formAgregar.reset();
            }
        });
        // Función de filtrado
        function filtrarProductos() {
            let input = document.getElementById('buscar');
            let filtro = input.value.toUpperCase();
            let tabla = document.getElementById('tablaProductos');
            let filas = tabla.getElementsByTagName('tr');
            
            for (let i = 1; i < filas.length; i++) {
                let mostrarFila = false;
                let codigo = filas[i].getElementsByTagName('td')[0];
                let nombre = filas[i].getElementsByTagName('td')[1];
                
                if (codigo && nombre) {
                    let textoCodigo = codigo.textContent || codigo.innerText;
                    let textoNombre = nombre.textContent || nombre.innerText;
                    
                    if (textoCodigo.toUpperCase().indexOf(filtro) > -1 || 
                        textoNombre.toUpperCase().indexOf(filtro) > -1) {
                        mostrarFila = true;
                    }
                }
                
                filas[i].style.display = mostrarFila ? '' : 'none';
            }
        }
    </script>

    <!-- Función del modal de editar -->
    <script>
        const modalEditar = document.getElementById('editModal');
        const btnCerrarEditar = document.getElementById('closeEditModal');
        const formEditar = document.getElementById('editForm');

        // Manejar botones de editar con event delegation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                const id = btn.getAttribute('data-id');
                fetch(`?action=inventario&method=obtenerCategoria&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const producto = data.producto;
                            document.getElementById('editId').value = producto.id_producto;
                            document.getElementById('editName').value = producto.nombre;
                            modalEditar.style.display = 'block';
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

        // Cerrar modal de editar con X
        btnCerrarEditar.addEventListener('click', function() {
            modalEditar.style.display = 'none';
            formEditar.reset();
        });

        // Cerrar modal de editar al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (e.target === modalEditar) {
                modalEditar.style.display = 'none';
                formEditar.reset();
            }
        });

        // Cerrar modales con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (modalEditar.style.display === 'block') {
                    modalEditar.style.display = 'none';
                    formEditar.reset();
                }
                if (modalAgregar.style.display === 'block') {
                    modalAgregar.style.display = 'none';
                    formAgregar.reset();
                }
            }
        });

        // Validación antes de enviar formulario de editar
        formEditar.addEventListener('submit', function(e) {
            const compra = parseFloat(precioCompraEdit.value);
            const venta = parseFloat(precioVentaEdit.value);
            
            if (venta <= compra) {
                e.preventDefault();
                Swal.fire('Advertencia', 'El precio de venta debe ser mayor al precio de compra', 'warning');
                return false;
            }
        });
    </script>

    <!-- Función de eliminar -->
    <script>
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
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `?action=inventario&method=eliminarCategoria&id=${id}`;
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>