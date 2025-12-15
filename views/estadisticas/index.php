<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'UPEL' ?> - Estadísticas</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <!-- Base Admin CSS -->
    <link rel="stylesheet" href="public/css/admin.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="public/css/stats.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alerts -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>

        <!-- Main Wrapper with no hardcoded width -->
        <main class="main-content">
            <div class="main-wrapper">
                <header class="stats-header">
                    <h1 class="stats-title"><?= $titulo ?? 'Panel de Estadísticas' ?></h1>
                    <p>Resumen general del sistema y métricas clave.</p>
                </header>

                <!-- Summary Cards Section -->
                <section class="summary-grid">
                    <!-- Users -->
                    <div class="summary-card card-users">
                        <div class="summary-info">
                            <h3>Usuarios</h3>
                            <div class="count"><?= $totales['usuarios'] ?? 0 ?></div>
                        </div>
                        <div class="summary-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="summary-card card-products">
                        <div class="summary-info">
                            <h3>Productos</h3>
                            <div class="count"><?= $totales['productos'] ?? 0 ?></div>
                        </div>
                        <div class="summary-icon">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>

                    <!-- Requests -->
                    <div class="summary-card card-requests">
                        <div class="summary-info">
                            <h3>Solicitudes</h3>
                            <div class="count"><?= $totales['solicitudes'] ?? 0 ?></div>
                        </div>
                        <div class="summary-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>

                    <!-- Offices -->
                    <div class="summary-card card-offices">
                        <div class="summary-info">
                            <h3>Oficinas</h3>
                            <div class="count"><?= $totales['oficinas'] ?? 0 ?></div>
                        </div>
                        <div class="summary-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </section>

                <div class="stats-container">
                    <!-- Gráfico original - Cantidad de Productos en el Almacén -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <span><i class="fas fa-archive"></i> Estado del Almacén</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="estadoChart"></canvas>
                        </div>
                    </div>

                    <!-- 2. Frecuencia de solicitudes por oficina (Torta) -->
                    <div class="chart-card">
                        <div class="chart-header">
                           <span><i class="fas fa-chart-pie"></i> Solicitudes por Oficina</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="frecuenciaSolicitudesChart"></canvas>
                        </div>
                    </div>

                    <!-- 1. Tipos de productos solicitados por oficina (Barras) -->
                    <div class="chart-card full-width">
                        <div class="chart-header">
                           <span><i class="fas fa-chart-bar"></i> Tipos de Productos más Solicitados</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="tiposProductosChart"></canvas>
                        </div>
                    </div>

                    <!-- 3. Cantidad de productos solicitados por oficina (Torta) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <span><i class="fas fa-boxes"></i> Volumen de Productos Totales</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="cantidadProductosChart"></canvas>
                        </div>
                    </div>

                    <!-- 4. Solicitudes rechazadas por oficina (Barras) -->
                    <div class="chart-card">
                        <div class="chart-header">
                           <span><i class="fas fa-times-circle"></i> Rechazos por Oficina</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="solicitudesRechazadasChart"></canvas>
                        </div>
                    </div>

                    <!-- 5. Usuarios por oficina (Barras) -->
                    <div class="chart-card full-width">
                        <div class="chart-header">
                            <span><i class="fas fa-users-cog"></i> Distribución de Usuarios</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="usuariosChart"></canvas>
                        </div>
                    </div>

                    <!-- 6. Gráfico de Correlación -->
                    <div class="chart-card full-width">
                        <div class="chart-header">
                            <span><i class="fas fa-project-diagram"></i> Análisis de Correlación</span>
                        </div>
                        <div class="chart-body">
                            <canvas id="correlacionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Incluir Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Colores para los gráficos (Modern Palette)
        const coloresPaleta = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf',
            '#dda20a', '#be2617', '#6f42c1', '#e83e8c', '#fd7e14'
        ];

        // Definiciones de Chart.js Defaults
        Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
        Chart.defaults.color = '#858796';

        // Gráfico de Productos en almacén
        const estadoData = {
            labels: [<?= implode(',', array_map(function($e) { return "'".ucfirst(str_replace('_', ' ', $e['nombre']))."'"; }, $estadisticas['por_estado'])) ?>],
            datasets: [{
                data: [<?= implode(',', array_column($estadisticas['por_estado'], 'cantidad')) ?>],
                backgroundColor: coloresPaleta.slice(0, <?= count($estadisticas['por_estado']) ?>),
                borderWidth: 0
            }]
        };

        const estadoChart = new Chart(document.getElementById('estadoChart'), {
            type: 'doughnut',
            data: estadoData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                }
            }
        });

        // Tipos de productos por oficina 
        <?php
        $oficinas = [];
        $tiposProductos = [];
        $dataPorTipo = [];

        foreach ($tiposProductosPorOficina as $item) {
            if (!in_array($item['oficina'], $oficinas)) {
                $oficinas[] = $item['oficina'];
            }
            if (!in_array($item['tipo_producto'], $tiposProductos)) {
                $tiposProductos[] = $item['tipo_producto'];
            }
        }

        foreach ($tiposProductos as $tipo) {
            $dataPorTipo[$tipo] = array_fill(0, count($oficinas), 0);
        }

        foreach ($tiposProductosPorOficina as $item) {
            $oficinaIndex = array_search($item['oficina'], $oficinas);
            $dataPorTipo[$item['tipo_producto']][$oficinaIndex] = $item['cantidad'];
        }
        ?>

        const tiposProductosChart = new Chart(document.getElementById('tiposProductosChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($oficinas) ?>,
                datasets: [
                    <?php $colorIndex = 0; foreach ($tiposProductos as $tipo): ?>
                    {
                        label: '<?= $tipo ?>',
                        data: <?= json_encode($dataPorTipo[$tipo]) ?>,
                        backgroundColor: coloresPaleta[<?= $colorIndex++ ?>],
                        borderRadius: 4
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Frecuencia de solicitudes por oficina
        const frecuenciaSolicitudesChart = new Chart(document.getElementById('frecuenciaSolicitudesChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($frecuenciaSolicitudes, 'oficina')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($frecuenciaSolicitudes, 'total_solicitudes')) ?>,
                    backgroundColor: coloresPaleta.slice(0, <?= count($frecuenciaSolicitudes) ?>),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                }
            }
        });

        // Cantidad de productos solicitados por oficina
        const cantidadProductosChart = new Chart(document.getElementById('cantidadProductosChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($cantidadProductos, 'oficina')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($cantidadProductos, 'total_productos')) ?>,
                    backgroundColor: coloresPaleta.slice(0, <?= count($cantidadProductos) ?>),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                }
            }
        });

        // Solicitudes rechazadas por oficina
        const solicitudesRechazadasChart = new Chart(document.getElementById('solicitudesRechazadasChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($solicitudesRechazadas, 'oficina')) ?>,
                datasets: [{
                    label: 'Solicitudes Rechazadas',
                    data: <?= json_encode(array_column($solicitudesRechazadas, 'total_rechazadas')) ?>,
                    backgroundColor: '#e74a3b',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Usuarios por oficina
        const usuariosChart = new Chart(document.getElementById('usuariosChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($usuariosPorOficina, 'oficina')) ?>,
                datasets: [{
                    label: 'Usuarios',
                    data: <?= json_encode(array_column($usuariosPorOficina, 'total_usuarios')) ?>,
                    backgroundColor: '#1cc88a',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Gráfico de Correlación 
        const correlacionChart = new Chart(document.getElementById('correlacionChart'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Oficinas',
                    data: [
                        <?php foreach ($datosCorrelacion as $dato): ?>
                        {
                            x: <?= $dato['total_usuarios'] ?>,
                            y: <?= $dato['total_solicitudes'] ?>,
                            r: <?= max(5, min(25, $dato['total_productos'] / 5)) ?>,
                            oficina: '<?= $dato['oficina'] ?>'
                        },
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.5)',
                    borderColor: '#4e73df',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#6e707e',
                        bodyColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const point = context.raw;
                                return `${point.oficina}: ${point.x} usr, ${point.y} solic.`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Número de Usuarios' },
                        beginAtZero: true,
                        grid: { borderDash: [2, 2] }
                    },
                    y: {
                        title: { display: true, text: 'Número de Solicitudes' },
                        beginAtZero: true,
                        grid: { borderDash: [2, 2] }
                    }
                }
            }
        });

        // Mostrar aletas
        <?php if (isset($_GET['success'])): ?>
            Swal.fire({
                title: 'Éxito',
                text: '<?= addslashes(htmlspecialchars(urldecode($_GET['success']))) ?>',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4e73df'
            });
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            Swal.fire({
                title: 'Error',
                text: '<?= addslashes(htmlspecialchars(urldecode($_GET['error']))) ?>',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#e74a3b'
            });
        <?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
