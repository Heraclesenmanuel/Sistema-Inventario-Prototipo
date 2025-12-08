<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'Sistema' ?> - Notificaciones</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/notificaciones.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php'; ?>
        
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1>Centro de Notificaciones</h1>
                    <p class="subtitle">Alertas de inventario y solicitudes pendientes</p>
                </div>
            </div>

            <!-- Controls -->
            <div class="controls-bar">
                <div class="search-box">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Buscar por código, producto o tipo..." onkeyup="filtrarNotificaciones()">
                </div>
            </div>

            <!-- Alerts List -->
            <div class="alerts-container">
                <table class="alerts-table" id="alertsTable">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto Detallado</th>
                            <th>Tipo</th>
                            <th>Fecha Registro</th>
                            <th>Disponibilidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($datosInven)): ?>
                            <?php foreach($datosInven as $dato): ?>
                                <?php 
                                    $stock = (int)$dato['un_disponibles'];
                                    $badgeClass = $stock < 10 ? 'badge-danger' : 'badge-warning';
                                    $badgeText = $stock < 10 ? '¡Stock Crítico!' : 'Stock Bajo';
                                    $icon = $stock < 10 ? 'alert-octagon' : 'alert-triangle';
                                ?>
                                <tr class="alert-row">
                                    <td data-label="Código" style="font-family: monospace; color: #616161;">
                                        #<?= htmlspecialchars($dato['codigo']) ?>
                                    </td>
                                    
                                    <td data-label="Producto">
                                        <div style="display: flex; flex-direction: column;">
                                            <span class="col-product"><?= htmlspecialchars($dato['nombre']) ?></span>
                                            <span style="font-size: 0.85rem; color: #9E9E9E;">
                                                Medida: <?= htmlspecialchars($dato['medida']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td data-label="Tipo">
                                        <span style="background: #F5F5F5; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; color: #616161;">
                                            <?= htmlspecialchars($dato['tipo_p']) ?>
                                        </span>
                                    </td>

                                    <td data-label="Fecha">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <i data-lucide="calendar" style="width: 14px; height: 14px; color: #BDBDBD;"></i>
                                            <span class="date-text"><?= htmlspecialchars($dato['fecha_r']) ?></span>
                                        </div>
                                    </td>
                                    
                                    <td data-label="Disponibilidad">
                                        <span class="stock-badge <?= $badgeClass ?>">
                                            <i data-lucide="<?= $icon ?>" style="width: 14px; height: 14px;"></i>
                                            <?= $stock ?> Unds - <?= $badgeText ?>
                                        </span>
                                    </td>
                                    
                                    <td data-label="Acciones">
                                        <div class="actions-cell">
                                            <button class="btn-icon edit" 
                                                    onclick="verDetalles(<?= htmlspecialchars(json_encode($dato)) ?>)"
                                                    title="Ver Detalles">
                                                <i data-lucide="eye"></i>
                                            </button>
                                            <button class="btn-icon delete" 
                                                    onclick="descartarAlerta(<?= $dato['id_producto'] ?>)"
                                                    title="Descartar Alerta">
                                                <i data-lucide="bell-off"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="noDataRow">
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div style="display: inline-block; padding: 1.5rem; background: #F5F5F5; border-radius: 50%; margin-bottom: 1rem;">
                                            <i data-lucide="check-circle" style="width: 48px; height: 48px; color: #4CAF50;"></i>
                                        </div>
                                        <h3>¡Todo en orden!</h3>
                                        <p>No hay alertas de inventario ni notificaciones pendientes.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Zero results from search -->
                <div id="noResults" class="empty-state" style="display: none;">
                    <i data-lucide="search-x" class="empty-icon-large"></i>
                    <h3>Sin coincidencias</h3>
                    <p>No encontramos alertas que coincidan con tu búsqueda.</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inicializar Iconos
        lucide.createIcons();

        // Filtrado en tiempo real
        function filtrarNotificaciones() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('alertsTable');
            const rows = table.getElementsByClassName('alert-row');
            const noResults = document.getElementById('noResults');
            let hasVisible = false;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.innerText || row.textContent;
                
                if (text.toUpperCase().indexOf(filter) > -1) {
                    row.style.display = "";
                    hasVisible = true;
                } else {
                    row.style.display = "none";
                }
            }
            
            // Toggle tabla/mensaje vacio
            if(rows.length > 0) {
                table.style.display = hasVisible ? "" : "none";
                noResults.style.display = hasVisible ? "none" : "block";
            }
        }

        // Acciones Mockup (ya que no hay backend especifico para "descartar")
        function descartarAlerta(id) {
            Swal.fire({
                title: '¿Descartar alerta?',
                text: "Esta alerta desaparecerá de tu panel.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3F51B5', // Azul UPEL
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simular eliminación visual
                    Swal.fire(
                        'Descartada',
                        'La alerta ha sido removida.',
                        'success'
                    ).then(() => {
                        // En un caso real, aquí iría el $.post para eliminar
                        location.reload(); 
                    });
                }
            })
        }

        function verDetalles(dato) {
            Swal.fire({
                title: 'Detalles del Producto',
                html: `
                    <div style="text-align: left; padding: 0 1rem;">
                        <p><strong>Producto:</strong> ${dato.nombre}</p>
                        <p><strong>Código:</strong> ${dato.codigo}</p>
                        <p><strong>Categoría:</strong> ${dato.tipo_p}</p>
                        <hr style="margin: 1rem 0; border-color: #eee;">
                        <p><strong>Stock Actual:</strong> <span style="color: ${dato.un_disponibles < 10 ? '#E44336' : '#FFC107'} font-weight: bold;">${dato.un_disponibles} ${dato.medida}</span></p>
                        <p><strong>Ultima revisión:</strong> ${dato.fecha_r}</p>
                    </div>
                `,
                confirmButtonColor: '#3F51B5',
                confirmButtonText: 'Entendido'
            });
        }
    </script>
</body>
</html>