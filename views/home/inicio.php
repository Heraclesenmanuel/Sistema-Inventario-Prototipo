<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Inventario UPEL - Portal Principal">
    <meta name="theme-color" content="#3F51B5">
    <title>Portal - Sistema de Inventario UPEL</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="public/css/inicio.css">
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <section class="hero" id="hero">
            <div class="hero-content">
                <h1 class="glitch" data-text="Bienvenido">Bienvenido</h1>
                <p class="subtitle">Al sistema de gestión de inventario de la UPEL-BQTO</p>
                
                <nav class="hero-buttons" role="navigation" aria-label="Navegación principal">
                    <a href="?action=inicio&method=home" class="btn btn-primary">
                        <i data-lucide="rocket" class="btn-icon"></i>
                        <span>Ir al Sistema</span>
                    </a>
                    <a href="#gallery" class="btn btn-secondary">
                        <i data-lucide="images" class="btn-icon"></i>
                        <span>Ver Galería</span>
                    </a>
                </nav>
            </div>
            
            <!-- Decoración Flotante -->
            <div class="hero-decoration" aria-hidden="true">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
            </div>
        </section>

        <!-- Galería de Materiales -->
        <section class="gallery" id="gallery">
            <h2 class="section-title">Galería de Materiales</h2>
            
            <div class="gallery-grid">
                <article class="gallery-item" style="animation-delay: 0s">
                    <img src="public/img/entraipb.jpg" alt="Entrada del Instituto Pedagógico de Barquisimeto" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Instalaciones</h3>
                        <span class="category">
                            <i data-lucide="building-2" class="category-icon"></i>
                            UPEL IPB
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.1s">
                    <img src="public/img/Nosotros.jfif" alt="Logo institucional UPEL" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Identidad</h3>
                        <span class="category">
                            <i data-lucide="shield" class="category-icon"></i>
                            Logo
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.2s">
                    <img src="public/img/Laboratorios.jfif" alt="Laboratorios equipados" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Equipamiento</h3>
                        <span class="category">
                            <i data-lucide="flask-conical" class="category-icon"></i>
                            Laboratorio
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.3s">
                    <img src="public/img/Espacios.jfif" alt="Espacios comunes y áreas de estudio" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Infraestructura</h3>
                        <span class="category">
                            <i data-lucide="home" class="category-icon"></i>
                            Espacios
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.4s">
                    <img src="public/img/Personal.jfif" alt="Personal docente y administrativo" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Equipo Humano</h3>
                        <span class="category">
                            <i data-lucide="users" class="category-icon"></i>
                            Personal
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.5s">
                    <img src="public/img/Egresados.jfif" alt="Egresados de la institución" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Comunidad</h3>
                        <span class="category">
                            <i data-lucide="graduation-cap" class="category-icon"></i>
                            Egresados
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.6s">
                    <img src="public/img/Locas.jpg" alt="Actividades estudiantiles" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Vida Estudiantil</h3>
                        <span class="category">
                            <i data-lucide="sparkles" class="category-icon"></i>
                            Actividades
                        </span>
                    </div>
                </article>
                
                <article class="gallery-item" style="animation-delay: 0.7s">
                    <img src="public/img/Tripi_Tropi.jpg" alt="Eventos y celebraciones" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>Eventos</h3>
                        <span class="category">
                            <i data-lucide="calendar-days" class="category-icon"></i>
                            Celebraciones
                        </span>
                    </div>
                </article>
            </div>
        </section>

        <!-- Características del Sistema -->
        <section class="info-section" aria-labelledby="features-title">
            <h2 id="features-title" class="visually-hidden">Características del Sistema</h2>
            
            <div class="info-cards">
                <article class="info-card">
                    <div class="card-icon">
                        <i data-lucide="clipboard-list" class="feature-icon"></i>
                    </div>
                    <h3>Solicitudes de Productos</h3>
                    <p>Gestiona y procesa solicitudes de manera eficiente</p>
                </article>
                
                <article class="info-card">
                    <div class="card-icon">
                        <i data-lucide="package-check" class="feature-icon"></i>
                    </div>
                    <h3>Despachos por Oficina</h3>
                    <p>Control detallado de entregas departamentales</p>
                </article>
                
                <article class="info-card">
                    <div class="card-icon">
                        <i data-lucide="database" class="feature-icon"></i>
                    </div>
                    <h3>Gestión de Inventario</h3>
                    <p>Administración completa de recursos y materiales</p>
                </article>
                
                <article class="info-card">
                    <div class="card-icon">
                        <i data-lucide="shield-check" class="feature-icon"></i>
                    </div>
                    <h3>Consultas entre Departamentos</h3>
                    <p>Comunicación segura y trazabilidad total</p>
                </article>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer" role="contentinfo">
            <p>Para la profesora Magda Perozo | 2025</p>
            
            <nav class="footer-links" aria-label="Enlaces del footer">
                <a href="?action=inicio&method=home">
                    <i data-lucide="home" class="footer-icon"></i>
                    <span>Inicio</span>
                </a>
                <a href="#gallery">
                    <i data-lucide="image" class="footer-icon"></i>
                    <span>Galería</span>
                </a>
                <a href="#contact">
                    <i data-lucide="mail" class="footer-icon"></i>
                    <span>Contacto</span>
                </a>
            </nav>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script>
        // Inicializar Lucide Icons
        lucide.createIcons();
        
        // Smooth Scroll para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Intersection Observer para animaciones al scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, observerOptions);
        
        // Observar elementos animables
        document.querySelectorAll('.gallery-item, .info-card, .stat-item').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>