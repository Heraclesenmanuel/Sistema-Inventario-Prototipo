<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión Administrativa UPEL - Panel de Control">
    <meta name="theme-color" content="#3F51B5">
    <title>Sistema de <?= APP_NAME ?? 'Inicio' ?></title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre = "Invitado";
if (!empty($_SESSION['nombre']) && is_string($_SESSION['nombre'])) {
    $nombre = htmlspecialchars(trim($_SESSION['nombre']), ENT_QUOTES, 'UTF-8');
}
$es_admin = isset($_SESSION['id_cargo']) && $_SESSION['id_cargo'] == 1;
?>

<body>
    <div class="dashboard">
        <?= require_once 'views/inc/heder.php'; ?>
        
        <main class="main-content">
            <!-- Welcome Section -->
            <section class="welcome-section">
                <article class="welcome-card">
                    <div class="welcome-image-container">
                        <img src="./public/img/graduados.jpg" alt="Bienvenida al sistema" class="welcome-image">
                        <div class="welcome-overlay"></div>
                    </div>
                    <div class="welcome-content">
                        <div class="welcome-icon">
                            <i data-lucide="sparkles" class="icon-welcome"></i>
                        </div>
                        <h1 class="welcome-title">
                            Bienvenid@, <?php echo $nombre; ?>
                        </h1>
                        <p class="welcome-subtitle">Al Sistema de Gestión Administrativa</p>
                        <p class="welcome-description">UPEL - Instituto Pedagógico de Barquisimeto</p>
                    </div>
                </article>
            </section>
            
            <!-- Quick Access Section -->
            <section class="quick-access-section">
                <header class="section-header">
                    <div class="header-content">
                        <i data-lucide="zap" class="header-icon"></i>
                        <h2 class="section-title">Accesos Rápidos</h2>
                    </div>
                    <p class="section-description">Accede rápidamente a las funciones principales del sistema</p>
                </header>
                
                <div class="quick-access-grid">
                    <!-- Inventario -->
                    <a href="?action=inventario&method=home" class="quick-access-card" data-category="inventory">
                        <div class="card-icon-wrapper">
                            <i data-lucide="package" class="card-icon"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Inventario</h3>
                            <p class="card-description">Gestión de productos y categorías</p>
                        </div>
                        <div class="card-arrow">
                            <i data-lucide="arrow-right" class="arrow-icon"></i>
                        </div>
                    </a>
                    <?php if($_SESSION['dpto'] != 2): ?>
                    <!-- Proveedores -->
                    <a href="?action=proveedor&method=home" class="quick-access-card" data-category="providers">
                        <div class="card-icon-wrapper">
                            <i data-lucide="truck" class="card-icon"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Proveedores</h3>
                            <p class="card-description">Administración de proveedores</p>
                        </div>
                        <div class="card-arrow">
                            <i data-lucide="arrow-right" class="arrow-icon"></i>
                        </div>
                    </a>
                    <?php if($_SESSION['dpto'] != 4): ?>
                    <!-- Oficinas -->
                    <a href="?action=oficinas&method=home" class="quick-access-card" data-category="offices">
                        <div class="card-icon-wrapper">
                            <i data-lucide="building-2" class="card-icon"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Oficinas</h3>
                            <p class="card-description">Gestión de departamentos</p>
                        </div>
                        <div class="card-arrow">
                            <i data-lucide="arrow-right" class="arrow-icon"></i>
                        </div>
                    </a>
                    <?php endif; ?>
                    <!-- Estadísticas -->
                    <a href="?action=reporte&method=home" class="quick-access-card" data-category="stats">
                        <div class="card-icon-wrapper">
                            <i data-lucide="bar-chart-3" class="card-icon"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Estadísticas</h3>
                            <p class="card-description">Reportes y análisis de datos</p>
                        </div>
                        <div class="card-arrow">
                            <i data-lucide="arrow-right" class="arrow-icon"></i>
                        </div>
                    </a>
                    <?php endif; ?>
                    <!-- Solicitudes -->
                    <a href="?action=solicitudes&method=home" class="quick-access-card" data-category="requests">
                        <div class="card-icon-wrapper">
                            <i data-lucide="file-text" class="card-icon"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Solicitudes</h3>
                            <p class="card-description">Gestión de peticiones</p>
                        </div>
                        <div class="card-arrow">
                            <i data-lucide="arrow-right" class="arrow-icon"></i>
                        </div>
                    </a>
                    
                    <!-- Algo más -->
                    <a href="?action=algo&method=users" class="quick-access-card" data-category="other">
                        <div class="card-icon-wrapper">
                            <i data-lucide="more-horizontal" class="card-icon"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Algo más</h3>
                            <p class="card-description">Funciones adicionales</p>
                        </div>
                        <div class="card-arrow">
                            <i data-lucide="arrow-right" class="arrow-icon"></i>
                        </div>
                    </a>
                </div>
            </section>
            
            <!-- Footer -->
            <footer class="main-footer">
                <div class="footer-content">
                    <p class="footer-text">
                        <i data-lucide="copyright" class="footer-icon"></i>
                        2025 UPEL - Instituto Pedagógico de Barquisimeto
                    </p>
                </div>
            </footer>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    
    <script>
        // Inicializar Lucide Icons
        lucide.createIcons();
        
        // Animación de entrada para las cards
        const cards = document.querySelectorAll('.quick-access-card');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        cards.forEach(card => {
            observer.observe(card);
        });
        
        // Hover effect para las cards
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const arrow = this.querySelector('.arrow-icon');
                if (arrow) {
                    arrow.style.transform = 'translateX(5px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                const arrow = this.querySelector('.arrow-icon');
                if (arrow) {
                    arrow.style.transform = 'translateX(0)';
                }
            });
        });
    </script>
</body>
</html>