<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Inicio' ?> - <?= $titulo ?></title>
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
                    <input type="text" id="buscar" name="buscar" placeholder="Ingrese el nombre o código del producto" onkeyup="filtrarProductos()">
                    <button name="add" id="add"><i class="fas fa-plus"></i> Agregar Producto</button>
                    <button name="importar" id="importar"><i class="fas fa-file-import"></i> Importar PDF</button>
                </div>
                <div class="list-Product">
                    <table id="tablaProductos" style="background-color: aliceblue; max-width: 95%;">
                        <thead style="background-color: aqua;">
                            <tr>
                                <th>Nombre</th>
                                <th>Presentacion</th>
                                <th>Fecha de Registro</th>
                                <th>Tipo</th>
                                <th>Unidades Disponibles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($datosInven)): ?>
                                <?php foreach($datosInven as $dato): ?>
                                    <?php 
                                    $totalUnidades = $dato['un_disponibles'];
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dato['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['medida']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['fecha_r']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['tipo']); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($dato['un_disponibles']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-editar" 
                                                data-id="<?php echo $dato['id_producto']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                class="btn btn-sm btn-danger btn-eliminar"
                                                title="Eliminar solicitud" 
                                                data-id="<?php echo $dato['id_producto']; ?>">
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
                                            <h5>No hay productos en inventario</h5>
                                            <p>No se encontraron productos registrados.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal de agregar producto -->
                <div id="productModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeAddModal">&times;</span>
                        <h2>Agregar Nuevo Producto</h2>
                        <br>
                        <form id="productForm" method="post">
                            <div class="form-group">
                                <label for="productName">Nombre del Producto:</label>
                                <input type="text" id="productName" name="productName" required>
                            </div>
                            
                            <div class="form-group">
                                <select id="productMeasure" name="productMeasure" required>
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
                                <label for="productStock">Unidades Disponibles:</label>
                                <input type="number" id="productStock" name="productStock" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="editStock">Tipo de producto:</label>
                                <select name="tipo_p" id="tipo_p" class="form-select-sm">
                                    <?php if(isset($tipos_p['success']) && $tipos_p['success'] && !empty($tipos_p['data'])): ?>
                                        <?php foreach($tipos_p['data'] as $tipo): ?>
                                            <option value=<?php echo $tipo['id_tipo']?>><?php echo $tipo['nombre']?></option>
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
                                <button type="submit" class="submit-btn" name="add">Guardar Producto</button>
                            </div>
                            
                        </form>
                    </div>
                </div>

                <!-- Modal de editar producto -->
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeEditModal">&times;</span>
                        <h2>Editar Producto</h2>
                        <form id="editForm" method="post" action="?action=inventario&method=actualizarProducto">
                            <input type="hidden" id="editId" name="editId">
                            
                            <div class="form-group">
                                <label for="productCode">Código:</label>
                                <input type="text" id="codigo" pattern="[0-9]*" name="codigo" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="editName">Nombre del Producto:</label>
                                <input type="text" id="editName" name="nombre" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="editMeasure">Unidad de Medida:</label>
                                <input type="text" id="editMeasure" name="medida" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="editStock">Unidades Disponibles:</label>
                                <input type="number" id="editStock" name="un_disponibles" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="editStock">Tipo de producto:</label>
                                <select name="tipo_p" id="tipo_p">
                                    <option value="Alimento" selected>Alimento</option>
                                    <option value="Limpieza">Limpieza</option>
                                    <option value="Electronicos">Electronicos</option>
                                    <option value="Oficina">Oficina</option>
                                    <option value="Material literario">Material literario</option>
                                </select>
                            </div>
                            <button type="submit" class="submit-btn">Actualizar Producto</button>
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
                fetch(`?action=inventario&method=obtenerProducto&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const producto = data.producto;
                            document.getElementById('editId').value = producto.id_producto;
                            document.getElementById('editCode').value = producto.codigo;
                            console.log("SADSA")
                            document.getElementById('editName').value = producto.nombre;
                            document.getElementById('editMeasure').value = producto.medida;
                            document.getElementById('editStock').value = producto.un_disponibles;
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
                            window.location.href = `?action=inventario&method=eliminarProducto&id=${id}`;
                        }
                    });
                });
            });
        });
    </script>

    <!-- Exportar a PDF -->
    <script>
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
                if(index < 6) {
                    headers.push(th.textContent);
                }
            });
            
            table.querySelectorAll('tbody tr').forEach(tr => {
                if(tr.querySelector('td[colspan]')) return;
                
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    if(index < 6) {
                        let text = td.textContent.trim();
                        if(index === 4 || index === 5) {
                            text = text.replace('$', '').replace(/\./g, '').replace(',', '.');
                            text = parseFloat(text).toFixed(2);
                        }
                        row.push(text);
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
                    fillColor: [50, 150, 250],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: { cellWidth: 60 }, 
                    1: { cellWidth: 'auto' },
                    2: { cellWidth: 80 },
                    3: { cellWidth: 60, halign: 'center' },
                    4: { cellWidth: 70, halign: 'right' },
                    5: { cellWidth: 80, halign: 'right' }
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