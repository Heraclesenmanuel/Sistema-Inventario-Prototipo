<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'UPEL' ?> - Estadísticas</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="public/css/admin.css">
<<<<<<< HEAD
    <link rel="stylesheet" href="public/css/estadisticas.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
=======
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .main-content {
            width: 85%;
            max-width: 85%;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 20px 0;
            width: 100%;
        }
        
        .chart-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .chart-header {
            background: #4e73df;
            color: white;
            padding: 15px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .chart-header i {
            margin-right: 8px;
        }
        
        .chart-body {
            padding: 20px;
            height: 350px;
            position: relative;
        }
        
        .chart-body canvas {
            max-height: 100%;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        @media (max-width: 1200px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .full-width {
                grid-column: 1;
            }
        }
    </style>
>>>>>>> d42897694361fe8c2147c1b73232393344293c4d
</head>
<body>
    <div class="dashboard">
        <?php include_once 'views/inc/heder.php' ?>

<<<<<<< HEAD
        <main class="main-content">
            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <i data-lucide="bar-chart-2" class="header-icon"></i>
                    <div>
                        <h2>Panel de Estadísticas</h2>
                        <p class="header-date">
                            <i data-lucide="calendar" class="date-icon"></i>
                            <?= date('d/m/Y') ?>
                        </p>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="stats-container">
                
                <!-- 1. Cantidad de Productos en Almacén -->
                <div class="chart-card">
                    <div class="chart-header">
                        <i data-lucide="box" class="chart-header-icon"></i>
                        <h3 class="chart-title">Inventario en Almacén</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="estadoChart"></canvas>
                    </div>
                </div>

                <!-- 2. Frecuencia de Solicitudes -->
                <div class="chart-card">
                    <div class="chart-header">
                        <i data-lucide="activity" class="chart-header-icon"></i>
                        <h3 class="chart-title">Frecuencia de Solicitudes</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="frecuenciaSolicitudesChart"></canvas>
                    </div>
                </div>

                <!-- 3. Tipos de Productos por Oficina -->
                <div class="chart-card full-width">
                    <div class="chart-header">
                        <i data-lucide="package-search" class="chart-header-icon"></i>
                        <h3 class="chart-title">Tipos de Productos Solicitados por Oficina</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="tiposProductosChart"></canvas>
                    </div>
                </div>

                <!-- 4. Volumen de Productos Solicitados -->
                <div class="chart-card">
                    <div class="chart-header">
                        <i data-lucide="pie-chart" class="chart-header-icon"></i>
                        <h3 class="chart-title">Volumen de Productos Solicitados</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="cantidadProductosChart"></canvas>
                    </div>
                </div>

                <!-- 5. Solicitudes Rechazadas -->
                <div class="chart-card">
                    <div class="chart-header">
                        <i data-lucide="x-circle" class="chart-header-icon" style="color: var(--rojo-vibrante);"></i>
                        <h3 class="chart-title">Solicitudes Rechazadas</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="solicitudesRechazadasChart"></canvas>
                    </div>
                </div>

                <!-- 6. Usuarios por Oficina -->
                <div class="chart-card full-width">
                    <div class="chart-header">
                        <i data-lucide="users" class="chart-header-icon"></i>
                        <h3 class="chart-title">Distribución de Usuarios por Oficina</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="usuariosChart"></canvas>
                    </div>
                </div>

                <!-- 7. Correlación -->
                <div class="chart-card full-width">
                    <div class="chart-header">
                        <i data-lucide="git-commit" class="chart-header-icon"></i>
                        <h3 class="chart-title">Correlación: Usuarios vs Solicitudes (Tamaño = Productos)</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="correlacionChart"></canvas>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Preparación de Datos en PHP (Segura) -->
    <?php
        // Helper para asegurar array
        if (!function_exists('safe_array')) {
            function safe_array($var) { return is_array($var) ? $var : []; }
        }

        // 1. Estados
        $dataEstados = safe_array($estadisticas['por_estado'] ?? []);
        $labels1 = array_map(function($e) { return ucfirst(str_replace('_', ' ', $e['nombre'])); }, $dataEstados);
        $values1 = array_column($dataEstados, 'cantidad');

        // 2. Tipos Productos (Lógica de transformación)
        $rawTipos = safe_array($tiposProductosPorOficina ?? []);
        $oficinas = [];
        $tipos = [];
        foreach ($rawTipos as $item) {
            if (!in_array($item['oficina'], $oficinas)) $oficinas[] = $item['oficina'];
            if (!in_array($item['tipo_producto'], $tipos)) $tipos[] = $item['tipo_producto'];
        }
        $datasetsTipos = [];
        foreach ($tipos as $t) {
            $dataRow = array_fill(0, count($oficinas), 0);
            foreach ($rawTipos as $item) {
                if ($item['tipo_producto'] === $t) {
                    $idx = array_search($item['oficina'], $oficinas);
                    if ($idx !== false) $dataRow[$idx] = $item['cantidad'];
                }
            }
            $datasetsTipos[] = ['label' => $t, 'data' => $dataRow];
        }

        // 3. Frecuencia
        $dataFrec = safe_array($frecuenciaSolicitudes ?? []);
        $labels3 = array_column($dataFrec, 'oficina');
        $values3 = array_column($dataFrec, 'total_solicitudes');

        // 4. Cantidad
        $dataCant = safe_array($cantidadProductos ?? []);
        $labels4 = array_column($dataCant, 'oficina');
        $values4 = array_column($dataCant, 'total_productos');

        // 5. Rechazadas
        $dataRech = safe_array($solicitudesRechazadas ?? []);
        $labels5 = array_column($dataRech, 'oficina');
        $values5 = array_column($dataRech, 'total_rechazadas');

        // 6. Usuarios
        $dataUsr = safe_array($usuariosPorOficina ?? []);
        $labels6 = array_column($dataUsr, 'oficina');
        $values6 = array_column($dataUsr, 'total_usuarios');

        // 7. Correlación
        $dataCorr = safe_array($datosCorrelacion ?? []);
        $scatterData = array_map(function($d) {
            return [
                'x' => $d['total_usuarios'],
                'y' => $d['total_solicitudes'],
                'r' => max(5, min(25, $d['total_productos'] / 3)),
                'oficina' => $d['oficina'],
                'productos' => $d['total_productos']
            ];
        }, $dataCorr);
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Iconos
            if(typeof lucide !== 'undefined') lucide.createIcons();

            // Paleta de Colores
            const chartColors = [
                '#3F51B5', '#2ECC71', '#FFC107', '#E44336', 
                '#9C27B0', '#00BCD4', '#FF9800', '#795548', 
                '#607D8B', '#E91E63'
            ];

            // Datos inyectados desde PHP
            const data1 = { labels: <?= json_encode($labels1) ?>, values: <?= json_encode($values1) ?> };
            const data2 = { oficinas: <?= json_encode($oficinas) ?>, datasets: <?= json_encode($datasetsTipos) ?> };
            const data3 = { labels: <?= json_encode($labels3) ?>, values: <?= json_encode($values3) ?> };
            const data4 = { labels: <?= json_encode($labels4) ?>, values: <?= json_encode($values4) ?> };
            const data5 = { labels: <?= json_encode($labels5) ?>, values: <?= json_encode($values5) ?> };
            const data6 = { labels: <?= json_encode($labels6) ?>, values: <?= json_encode($values6) ?> };
            const data7 = <?= json_encode($scatterData) ?>;

            // Configuración Global Chart.js
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#5F6368';
            Chart.defaults.scale.grid.color = '#F0F0F0';

            // --- Renderizadores ---

            // 1. Inventario (Doughnut)
            if (document.getElementById('estadoChart')) {
                new Chart(document.getElementById('estadoChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data1.labels,
                        datasets: [{
                            data: data1.values,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // 2. Frecuencia Solicitudes (Pie)
            if (document.getElementById('frecuenciaSolicitudesChart')) {
                new Chart(document.getElementById('frecuenciaSolicitudesChart'), {
                    type: 'pie',
                    data: {
                        labels: data3.labels,
                        datasets: [{
                            data: data3.values,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // 3. Tipos Productos (Bar Stacked-ish)
            if (document.getElementById('tiposProductosChart')) {
                const datasets = data2.datasets.map((ds, i) => ({
                    label: ds.label,
                    data: ds.data,
                    backgroundColor: chartColors[i % chartColors.length],
                    borderRadius: 4
                }));

                new Chart(document.getElementById('tiposProductosChart'), {
                    type: 'bar',
                    data: {
                        labels: data2.oficinas,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        barPercentage: 0.6,
                        plugins: { legend: { position: 'top' } },
                        scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                    }
                });
            }

            // 4. Cantidad Productos (Polar)
            if (document.getElementById('cantidadProductosChart')) {
                new Chart(document.getElementById('cantidadProductosChart'), {
                    type: 'polarArea',
                    data: {
                        labels: data4.labels,
                        datasets: [{
                            data: data4.values,
                            backgroundColor: chartColors.map(c => c + 'AA'),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // 5. Rechazadas (Bar Horizontal)
            if (document.getElementById('solicitudesRechazadasChart')) {
                new Chart(document.getElementById('solicitudesRechazadasChart'), {
                    type: 'bar',
                    data: {
                        labels: data5.labels,
                        datasets: [{
                            label: 'Rechazadas',
                            data: data5.values,
                            backgroundColor: '#E44336',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // 6. Usuarios (Bar)
            if (document.getElementById('usuariosChart')) {
                new Chart(document.getElementById('usuariosChart'), {
                    type: 'bar',
                    data: {
                        labels: data6.labels,
                        datasets: [{
                            label: 'Usuarios Activos',
                            data: data6.values,
                            backgroundColor: '#2ECC71',
                            borderRadius: 4,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // 7. Correlación (Scatter)
            if (document.getElementById('correlacionChart')) {
                new Chart(document.getElementById('correlacionChart'), {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: 'Oficinas',
                            data: data7,
                            backgroundColor: 'rgba(63, 81, 181, 0.6)',
                            borderColor: '#3F51B5',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const p = ctx.raw;
                                        return `${p.oficina}: ${p.x} Usuarios, ${p.y} Solicitudes`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Usuarios' }, beginAtZero: true },
                            y: { title: { display: true, text: 'Solicitudes' }, beginAtZero: true }
                        }
                    }
                });
            }
        });
    </script>
=======
        <div class="main-wrapper" style="margin-left: 10px; padding: 20px;">
            <main class="main-content">
                <h1><?= $titulo ?? 'Estadísticas' ?></h1>
                <br>

                <div class="stats-container">
                    <!-- Gráfico original - Cantidad de Productos en el Almacén -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <i class="fas fa-chart-pie"></i> Cantidad de Productos en el Almacén
                        </div>
                        <div class="chart-body">
                            <canvas id="estadoChart"></canvas>
                        </div>
                    </div>

                    <!-- 2. Frecuencia de solicitudes por oficina (Torta) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <i class="fas fa-chart-pie"></i> Frecuencia de Solicitudes por Oficina
                        </div>
                        <div class="chart-body">
                            <canvas id="frecuenciaSolicitudesChart"></canvas>
                        </div>
                    </div>

                    <!-- 1. Tipos de productos solicitados por oficina (Barras) -->
                    <div class="chart-card full-width">
                        <div class="chart-header">
                            <i class="fas fa-chart-bar"></i> Tipos de Productos Solicitados por Oficina
                        </div>
                        <div class="chart-body">
                            <canvas id="tiposProductosChart"></canvas>
                        </div>
                    </div>

                    <!-- 3. Cantidad de productos solicitados por oficina (Torta) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <i class="fas fa-chart-pie"></i> Cantidad de Productos Solicitados por Oficina
                        </div>
                        <div class="chart-body">
                            <canvas id="cantidadProductosChart"></canvas>
                        </div>
                    </div>

                    <!-- 4. Solicitudes rechazadas por oficina (Barras) -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <i class="fas fa-chart-bar"></i> Solicitudes Rechazadas por Oficina
                        </div>
                        <div class="chart-body">
                            <canvas id="solicitudesRechazadasChart"></canvas>
                        </div>
                    </div>

                    <!-- 5. Usuarios por oficina (Barras) -->
                    <div class="chart-card full-width">
                        <div class="chart-header">
                            <i class="fas fa-chart-bar"></i> Usuarios por Oficina
                        </div>
                        <div class="chart-body">
                            <canvas id="usuariosChart"></canvas>
                        </div>
                    </div>

                    <!-- 6. Gráfico de Correlación -->
                    <div class="chart-card full-width">
                        <div class="chart-header">
                            <i class="fas fa-project-diagram"></i> Correlación: Usuarios vs Solicitudes por Oficina
                        </div>
                        <div class="chart-body">
                            <canvas id="correlacionChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Incluir Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Colores para los gráficos
        const coloresPaleta = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf',
            '#dda20a', '#be2617', '#6f42c1', '#e83e8c', '#fd7e14'
        ];

        // Gráfico de Productos en almacén
        const estadoData = {
            labels: [<?= implode(',', array_map(function($e) { return "'".ucfirst(str_replace('_', ' ', $e['nombre']))."'"; }, $estadisticas['por_estado'])) ?>],
            datasets: [{
                data: [<?= implode(',', array_column($estadisticas['por_estado'], 'cantidad')) ?>],
                backgroundColor: coloresPaleta.slice(0, <?= count($estadisticas['por_estado']) ?>),
                hoverBackgroundColor: coloresPaleta.slice(0, <?= count($estadisticas['por_estado']) ?>).map(c => c + 'dd')
            }]
        };

        const estadoChart = new Chart(document.getElementById('estadoChart'), {
            type: 'doughnut',
            data: estadoData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
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
                        backgroundColor: coloresPaleta[<?= $colorIndex++ ?>]
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
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
                    backgroundColor: coloresPaleta.slice(0, <?= count($frecuenciaSolicitudes) ?>)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} solicitudes (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Cantidad de productos solicitados por oficina
        const cantidadProductosChart = new Chart(document.getElementById('cantidadProductosChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($cantidadProductos, 'oficina')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($cantidadProductos, 'total_productos')) ?>,
                    backgroundColor: coloresPaleta.slice(0, <?= count($cantidadProductos) ?>)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} productos (${percentage}%)`;
                            }
                        }
                    }
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
                    borderColor: '#be2617',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
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
                    borderColor: '#17a673',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
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
                            r: <?= max(5, min(20, $dato['total_productos'] / 5)) ?>,
                            oficina: '<?= $dato['oficina'] ?>'
                        },
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.6)',
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
                        callbacks: {
                            label: function(context) {
                                const point = context.raw;
                                return [
                                    `Oficina: ${point.oficina}`,
                                    `Usuarios: ${point.x}`,
                                    `Solicitudes: ${point.y}`,
                                    `Productos: ${Math.round(point.r * 5)}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Número de Usuarios'
                        },
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Solicitudes'
                        },
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
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
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            Swal.fire({
                title: 'Error',
                text: '<?= addslashes(htmlspecialchars(urldecode($_GET['error']))) ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
>>>>>>> d42897694361fe8c2147c1b73232393344293c4d
</body>
</html>
