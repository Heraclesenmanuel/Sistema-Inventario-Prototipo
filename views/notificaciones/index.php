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
                <div class="list-Product">
                    <table id="tablaProductos" style="background-color: aliceblue; max-width: 95%;">
                        <thead style="background-color: aqua;">
                            <tr>
                                <th>etc</th>
                                <th>etc</th>
                                <th>etc</th>
                                <th>etc</th>
                                <th>etc</th>
                                <th>etc</th>
                                <th>etc</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($datosInven)): ?>
                                <?php foreach($datosInven as $dato): ?>
                                    <?php 
                                    $totalUnidades = $dato['un_disponibles'];
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dato['codigo']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['medida']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['fecha_r']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['tipo_p']); ?></td>
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
                                            <h5>No hay notificaciones entrantes</h5>
                                            <p>No se encontraron notificaciones para tu oficina.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <!-- Funciones del modal de agregar -->
    <script>
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
</body>
</html>