<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'APP' ?> - <?= $titulo ?? 'Esta' ?></title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?= include_once 'views/inc/heder.php' ?>

        <div class="main-wrapper" style="margin-left: 10px; padding: 20px;">
            <main class="main-content">
                <h1><?= $titulo ?? 'Estadísticas' ?></h1>
                <br>

                <div class="row mb-4" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <!-- Gráfico de torta -->
                    <div class="col-md-6" style="flex: 1; min-width: 400px;">
                        <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Cantidad de Productos en el Almacen</h5>
                            </div>
                        <div class="card">
                            <div class="card-body">
                                <canvas id="estadoChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen estadístico -->
                    <div class="col-md-6" style="flex: 1; min-width: 300px;">
                        <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Resumen</h5>
                            </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Porcentaje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($estadisticas['por_estado'] as $estado): ?>
                                                <tr>
                                                    <td><?= ucfirst(str_replace('_', ' ', $estado['nombre'])) ?></td>
                                                    <td><?= $estado['cantidad'] ?></td>
                                                    <td><?= $estado['porcentaje'] ?>%</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer>
                <div class="footer-content" style="margin-left: 250px; padding: 20px;">
                    <p>&copy; 2025 <?= APP_NAME ?? 'APP' ?> - <?= $titulo ?? 'Esta' ?>. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
</body>

<!-- Incluir Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Datos para el gráfico de torta
const estadoData = {
    labels: [<?= implode(',', array_map(function($e) { return "'".ucfirst(str_replace('_', ' ', $e['nombre']))."'"; }, $estadisticas['por_estado'])) ?>],
    datasets: [{
        data: [<?= implode(',', array_column($estadisticas['por_estado'], 'cantidad')) ?>],
        backgroundColor: [
            '#4e73df', // Planificado - azul
            '#f6c23e', // En progreso - amarillo
            '#1cc88a', // Completado - verde
            '#e74a3b'  // Cancelado - rojo
        ],
        hoverBackgroundColor: [
            '#2e59d9',
            '#dda20a',
            '#17a673',
            '#be2617'
        ]
    }]
};

// Configuración del gráfico
const config = {
    type: 'doughnut',
    data: estadoData,
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            },
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
};

// Renderizar el gráfico
window.onload = function() {
    const ctx = document.getElementById('estadoChart').getContext('2d');
    new Chart(ctx, config);
    
    // Mostrar mensajes de éxito/error con SweetAlert2
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
};
</script>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>
