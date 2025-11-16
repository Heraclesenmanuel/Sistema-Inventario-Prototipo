<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/inicio.css">
    <title>Inicio - Portal</title>
</head>
<body>
    <div class="container">
        <!-- Seccion de presentacion -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="glitch" data-text="Bienvenido">Bienvenido</h1>
                <p class="subtitle">Al sistema de gestion de inventario de la UPEL-BQTO</p>
                <div class="hero-buttons">
                    <a href="?action=inicio&method=home" class="btn btn-primary">
                        <span>🚀</span> Ir al Sistema
                    </a>
                    <a href="#gallery" class="btn btn-secondary">
                        <span>📸</span> Ver Galería
                    </a>
                </div>
            </div>
            <div class="hero-decoration">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
            </div>
        </section>

        <!-- Seccion de galeria -->
        <section class="gallery" id="gallery">
            <h2 class="section-title">Galería de Materiales</h2>
            <div class="gallery-grid">
                <div class="gallery-item" style="animation-delay: 0s">
                    <img src="public/img/entraipb.jpg" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">UPEL IPB</span>
                    </div>
                </div>
                <div class="gallery-item" style="animation-delay: 0.1s">
                    <img src="public/img/Nosotros.jfif" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Logo</span>
                    </div>
                </div>
                <div class="gallery-item" style="animation-delay: 0.2s">
                    <img src="public/img/Laboratorios.jfif" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Laboratorio</span>
                    </div>
                </div>
                <div class="gallery-item" style="animation-delay: 0.3s">
                    <img src="public/img/Espacios.jfif" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Espacios</span>
                    </div>
                </div>
                <div class="gallery-item" style="animation-delay: 0.4s">
                    <img src="public/img/Personal.jfif" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Personal</span>
                    </div>
                </div>
                <div class="gallery-item" style="animation-delay: 0.5s">
                    <img src="public/img/Egresados.jfif" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Egresados</span>
                    </div>
                </div>
                                <div class="gallery-item" style="animation-delay: 0.5s">
                    <img src="public/img/Locas.jpg" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Locas</span>
                    </div>
                </div>
                                <div class="gallery-item" style="animation-delay: 0.5s">
                    <img src="public/img/Tripi_Tropi.jpg" alt="">
                    <div class="gallery-overlay">
                        <h3></h3>
                        <span class="category">Tripi Tropi</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cartas de informacion -->
        <section class="info-section">
            <div class="info-cards">
                <div class="info-card">
                    <div class="card-icon">💡</div>
                    <h3>Solicitudes de Productos</h3>
                    <p></p>
                </div>
                <div class="info-card">
                    <div class="card-icon">⚡</div>
                    <h3>Despachos por Oficina</h3>
                    <p></p>
                </div>
                <div class="info-card">
                    <div class="card-icon">🎯</div>
                    <h3>Gestion de Inventario</h3>
                    <p></p>
                </div>
                <div class="info-card">
                    <div class="card-icon">🔒</div>
                    <h3>Consultas entre departamentos</h3>
                    <p></p>
                </div>
            </div>
        </section>

        <!-- Seccion de estadisticas -->
        <section class="stats-section">
            <div class="stats-container">
                <div class="stat-item">
                    <h2 class="stat-number">100+</h2>
                    <p class="stat-label">Peticiones completadas</p>
                </div>
                <div class="stat-item">
                    <h2 class="stat-number">50+</h2>
                    <p class="stat-label">Aliados</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <p>Para la profesora Magda Perozo| 2025</p>
            <div class="footer-links">
                <a href="?action=inicio&method=home">Inicio</a>
                <a href="#gallery">Galería</a>
                <a href="#contact">Contacto</a>
            </div>
        </footer>
    </div>
</body>
</html>