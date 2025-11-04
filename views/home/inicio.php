<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Portal</title>
    <style>
        :root {
            --azul-anil: #3F51B5;
            --blanco: #FFFFFF;
            --verde-esmeralda: #2ECC71;
            --amarillo-ambar: #FFC107;
            --rojo-vibrante: #E44336;
            --azul-cielo: #BBDEFB;
            --gris-fondo: #f5f5f5;
            --texto-oscuro: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--azul-cielo) 0%, var(--blanco) 50%, var(--gris-fondo) 100%);
            color: var(--texto-oscuro);
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Seccion de presentacion*/
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--azul-anil) 0%, var(--verde-esmeralda) 50%, var(--azul-cielo) 100%);
        }

        .hero-content {
            text-align: center;
            z-index: 2;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glitch {
            font-size: 5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            color: var(--blanco);
            text-shadow: 
                3px 3px 0px var(--amarillo-ambar),
                -2px -2px 0px var(--azul-cielo);
            animation: glitch 3s infinite;
        }

        @keyframes glitch {
            0%, 100% {
                text-shadow: 
                    3px 3px 0px var(--amarillo-ambar),
                    -2px -2px 0px var(--azul-cielo);
            }
            25% {
                text-shadow: 
                    -2px 2px 0px var(--rojo-vibrante),
                    2px -2px 0px var(--verde-esmeralda);
            }
            50% {
                text-shadow: 
                    2px -2px 0px var(--amarillo-ambar),
                    -2px 2px 0px var(--azul-cielo);
            }
            75% {
                text-shadow: 
                    -2px -2px 0px var(--verde-esmeralda),
                    2px 2px 0px var(--rojo-vibrante);
            }
        }

        .subtitle {
            font-size: 1.5rem;
            margin-bottom: 3rem;
            color: var(--blanco);
            opacity: 0.95;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1.2rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
            z-index: 0;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn span {
            font-size: 1.3rem;
            z-index: 1;
            position: relative;
        }

        .btn-primary {
            background: var(--amarillo-ambar);
            color: var(--texto-oscuro);
            box-shadow: 0 10px 40px rgba(255, 193, 7, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(255, 193, 7, 0.6);
            background: #FFD54F;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.95);
            color: var(--azul-anil);
            border: 2px solid var(--azul-cielo);
        }

        .btn-secondary:hover {
            background: var(--azul-cielo);
            color: var(--azul-anil);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(187, 222, 251, 0.5);
        }

        /* Decoracion de presentacion */
        .hero-decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            backdrop-filter: blur(10px);
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 10%;
            background: rgba(46, 204, 113, 0.2);
            border: 3px solid var(--verde-esmeralda);
            animation: float 6s ease-in-out infinite;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: 20%;
            right: 15%;
            background: rgba(255, 193, 7, 0.2);
            border: 3px solid var(--amarillo-ambar);
            animation: float 8s ease-in-out infinite reverse;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 10%;
            background: rgba(187, 222, 251, 0.3);
            border: 3px solid var(--azul-cielo);
            animation: float 7s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(180deg);
            }
        }

        /* Seccion de galeria*/
        .gallery {
            padding: 5rem 2rem;
            background: var(--gris-fondo);
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, var(--azul-anil) 0%, var(--verde-esmeralda) 50%, var(--amarillo-ambar) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 0 1rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
            height: 350px;
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 3px solid transparent;
            transition: all 0.4s ease;
        }

        .gallery-item:nth-child(6n+1) { border-color: var(--azul-anil); }
        .gallery-item:nth-child(6n+2) { border-color: var(--verde-esmeralda); }
        .gallery-item:nth-child(6n+3) { border-color: var(--amarillo-ambar); }
        .gallery-item:nth-child(6n+4) { border-color: var(--rojo-vibrante); }
        .gallery-item:nth-child(6n+5) { border-color: var(--azul-cielo); }
        .gallery-item:nth-child(6n+6) { border-color: var(--azul-anil); }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(to top, rgba(63, 81, 181, 0.95), transparent);
            transform: translateY(100%);
            transition: transform 0.4s ease;
        }

        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }

        .gallery-overlay h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--blanco);
        }

        .category {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--amarillo-ambar);
            color: var(--texto-oscuro);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Seccion de informacion*/
        .info-section {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, var(--blanco) 0%, var(--azul-cielo) 100%);
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .info-card {
            background: var(--blanco);
            padding: 2.5rem;
            border-radius: 20px;
            text-align: center;
            transition: all 0.4s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--azul-anil), var(--verde-esmeralda), var(--amarillo-ambar), var(--rojo-vibrante));
        }

        .info-card:nth-child(1):hover {
            box-shadow: 0 20px 60px rgba(63, 81, 181, 0.3);
        }

        .info-card:nth-child(2):hover {
            box-shadow: 0 20px 60px rgba(46, 204, 113, 0.3);
        }

        .info-card:nth-child(3):hover {
            box-shadow: 0 20px 60px rgba(255, 193, 7, 0.3);
        }

        .info-card:nth-child(4):hover {
            box-shadow: 0 20px 60px rgba(228, 67, 54, 0.3);
        }

        .info-card:hover {
            transform: translateY(-10px);
        }

        .card-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .info-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--azul-anil);
        }

        .info-card p {
            color: var(--texto-oscuro);
            line-height: 1.6;
            opacity: 0.8;
        }

        /* seccion de estadisticas */
        .stats-section {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, var(--azul-anil) 0%, var(--verde-esmeralda) 100%);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .stat-item {
            animation: fadeInUp 1s ease;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            border: 2px solid rgba(255,255,255,0.2);
            transition: all 0.4s ease;
        }

        .stat-item:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
            border-color: var(--amarillo-ambar);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            color: var(--amarillo-ambar);
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        }

        .stat-label {
            font-size: 1.2rem;
            color: var(--blanco);
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Footer */
        .footer {
            padding: 3rem 2rem;
            background: var(--azul-anil);
            text-align: center;
        }

        .footer p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: var(--blanco);
            opacity: 0.9;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--amarillo-ambar);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--verde-esmeralda);
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-links a:hover {
            color: var(--azul-cielo);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .glitch {
                font-size: 3rem;
            }
            
            .subtitle {
                font-size: 1.2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Seccion de presentacion -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="glitch" data-text="Bienvenido">Bienvenido</h1>
                <p class="subtitle">Ezequiel, tu espacio personal</p>
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
                <?php
                //  aca se puede cambiar las URLs de las  imágenes
                $images = [
                    [
                        "url" => "https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&h=500&fit=crop",
                        "title" => "Tecnología",
                        "category" => "Tech"
                    ],
                    [
                        "url" => "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500&h=500&fit=crop",
                        "title" => "Desarrollo",
                        "category" => "Code"
                    ],
                    [
                        "url" => "https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=500&h=500&fit=crop",
                        "title" => "Innovación",
                        "category" => "Innovation"
                    ],
                    [
                        "url" => "https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=500&h=500&fit=crop",
                        "title" => "Código",
                        "category" => "Programming"
                    ],
                    [
                        "url" => "https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=500&h=500&fit=crop",
                        "title" => "Laptop",
                        "category" => "Work"
                    ],
                    [
                        "url" => "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500&h=500&fit=crop",
                        "title" => "Setup",
                        "category" => "Workspace"
                    ]
                ];

                foreach ($images as $index => $image) {
                    echo '<div class="gallery-item" style="animation-delay: ' . ($index * 0.1) . 's">';
                    echo '<img src="' . $image["url"] . '" alt="' . $image["title"] . '">';
                    echo '<div class="gallery-overlay">';
                    echo '<h3>' . $image["title"] . '</h3>';
                    echo '<span class="category">' . $image["category"] . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Cartas de  informacion -->
        <section class="info-section">
            <div class="info-cards">
                <?php
                $cards = [
                    [
                        "icon" => "💡",
                        "title" => "Innovación",
                        "description" => "Creando soluciones únicas y personalizadas"
                    ],
                    [
                        "icon" => "⚡",
                        "title" => "Velocidad",
                        "description" => "Rendimiento optimizado al máximo"
                    ],
                    [
                        "icon" => "🎯",
                        "title" => "Precisión",
                        "description" => "Atención a cada detalle de la solicitud"
                    ],
                    [
                        "icon" => "🔒",
                        "title" => "Seguridad",
                        "description" => "Protección de datos garantizada"
                    ]
                ];

                foreach ($cards as $card) {
                    echo '<div class="info-card">';
                    echo '<div class="card-icon">' . $card["icon"] . '</div>';
                    echo '<h3>' . $card["title"] . '</h3>';
                    echo '<p>' . $card["description"] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Seccion de estadisticas -->
        <section class="stats-section">
            <div class="stats-container">
                <?php
                $stats = [
                    ["number" => "100+", "label" => "Peticiones completadas"],
                    ["number" => "50+", "label" => "Aliados"],
                    ["number" => "100%", "label" => "Del personal esta capasitado"],
                    ["number" => "99%", "label" => "De eficacia"]
                ];

                foreach ($stats as $stat) {
                    echo '<div class="stat-item">';
                    echo '<h2 class="stat-number">' . $stat["number"] . '</h2>';
                    echo '<p class="stat-label">' . $stat["label"] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <p>Hecho por Ezequiel para mi amigo Heracles ❤️ | <?php echo date("Y"); ?></p>
            <div class="footer-links">
                <a href="?action=inicio&method=home">Inicio</a>
                <a href="#gallery">Galería</a>
                <a href="#contact">Contacto</a>
            </div>
        </footer>
    </div>
</body>
</html>