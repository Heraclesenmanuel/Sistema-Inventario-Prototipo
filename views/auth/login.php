<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Inventario UPEL - Inicio de Sesión">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?> - Iniciar Sesión</title>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Estilos -->
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
</head>
<body>
    <!-- Contenedor Principal -->
    <main class="login-container" role="main">
        <article class="login-card">
            <!-- Header con Logo -->
            <header class="logo-section">
                <div class="logo-icon">
                    <i data-lucide="shield-check" class="icon-logo"></i>
                </div>
                <h1 class="login-title">Bienvenido</h1>
                <p class="login-subtitle">Sistema de Inventario UPEL</p>
            </header>
            
            <!-- Formulario de Login -->
            <form action="?action=inicio&method=loginAuthenticate" method="post" class="login-form" novalidate>
                
                <!-- Alertas -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="alert-circle" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">Error de autenticación</strong>
                            <p class="alert-message">Usuario o contraseña incorrectos</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['state'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i data-lucide="check-circle" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">¡Éxito!</strong>
                            <p class="alert-message">Tu contraseña fue cambiada exitosamente</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Campo de Cédula -->
                <div class="form-group">
                    <label for="user" class="form-label">
                        <i data-lucide="user" class="label-icon"></i>
                        <span>Cédula de Identidad</span>
                    </label>
                    <div class="input-wrapper">
                        <i data-lucide="id-card" class="input-icon"></i>
                        <input 
                            type="text" 
                            id="user" 
                            name="user" 
                            class="form-input"
                            placeholder="V-12345678" 
                            required
                            autocomplete="username"
                            aria-label="Cédula de identidad"
                            aria-required="true"
                        >
                    </div>
                </div>
            
                <!-- Campo de Contraseña -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i data-lucide="lock" class="label-icon"></i>
                        <span>Contraseña</span>
                    </label>
                    <div class="input-wrapper password-wrapper">
                        <i data-lucide="key" class="input-icon"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input"
                            placeholder="Ingresa tu contraseña" 
                            required
                            autocomplete="current-password"
                            aria-label="Contraseña"
                            aria-required="true"
                        >
                        <button 
                            type="button" 
                            class="btn-toggle" 
                            onclick="togglePassword()"
                            aria-label="Mostrar u ocultar contraseña"
                        >
                            <i data-lucide="eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Botón de Submit -->
                <button type="submit" name="init" class="btn-submit">
                    <i data-lucide="log-in" class="btn-icon"></i>
                    <span>Iniciar Sesión</span>
                </button>
            </form>
            
            <!-- Footer con Links -->
            <footer class="footer-links">
                <a href="?action=inicio&method=forgotPassw" class="link-forgot">
                    <i data-lucide="help-circle" class="link-icon"></i>
                    <span>¿Olvidaste tu contraseña?</span>
                </a>
            </footer>
        </article>
        
        <!-- Decoración de Fondo -->
        <div class="background-decoration" aria-hidden="true">
            <div class="decoration-circle circle-1"></div>
            <div class="decoration-circle circle-2"></div>
            <div class="decoration-circle circle-3"></div>
        </div>
    </main>
    
    <!-- Scripts -->
    <script>
        // Inicializar Lucide Icons
        lucide.createIcons();
        
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }
            
            // Re-render icon
            lucide.createIcons();
        }
        
        // Form Validation Enhancement
        const form = document.querySelector('.login-form');
        const inputs = form.querySelectorAll('.form-input');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });
            
            input.addEventListener('invalid', function(e) {
                e.preventDefault();
                this.classList.add('is-invalid');
            });
            
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>