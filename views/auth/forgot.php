<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Inventario UPEL - Recuperar Contraseña">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?> - Recuperar Contraseña</title>
    
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
                    <i data-lucide="mail-question" class="icon-logo"></i>
                </div>
                <h1 class="login-title">Recuperar Contraseña</h1>
                <p class="login-subtitle">Ingresa tus credenciales para recuperar el acceso</p>
            </header>
            
            <!-- Formulario de Recuperación -->
            <form action="?action=inicio&method=sendPasswRequest" method="post" class="login-form" novalidate>
                
                <!-- Alertas -->
                <?php if (isset($_GET['error']) && $_GET['error']==1): ?>
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="alert-circle" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">Credenciales no encontradas</strong>
                            <p class="alert-message">Correo, usuario o cédula incorrectos</p>
                        </div>
                    </div>
                <?php elseif (isset($_GET['error']) && $_GET['error']==2): ?>
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="server-crash" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">Error del servidor</strong>
                            <p class="alert-message">Error accediendo a nuestros servidores, por favor inténtelo de nuevo en un momento</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Campo de Credenciales -->
                <div class="form-group">
                    <label for="user" class="form-label">
                        <i data-lucide="user-search" class="label-icon"></i>
                        <span>Credenciales</span>
                    </label>
                    <div class="input-wrapper">
                        <i data-lucide="at-sign" class="input-icon"></i>
                        <input 
                            type="text" 
                            id="user" 
                            name="user" 
                            class="form-input"
                            placeholder="Correo, cédula o usuario" 
                            required
                            minlength="7"
                            autocomplete="username"
                            aria-label="Credenciales de recuperación"
                            aria-required="true"
                        >
                    </div>
                    <small class="form-hint">Ingresa tu correo electrónico, cédula o nombre de usuario</small>
                </div>
                
                <!-- Botón de Submit -->
                <button type="submit" name="init" class="btn-submit">
                    <i data-lucide="send" class="btn-icon"></i>
                    <span>Enviar Código</span>
                </button>
            </form>
            
            <!-- Footer con Links -->
            <footer class="footer-links">
                <a href="?action=inicio&method=login" class="link-forgot">
                    <i data-lucide="arrow-left" class="link-icon"></i>
                    <span>Volver al inicio de sesión</span>
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
        
        // Form Validation Enhancement
        const form = document.querySelector('.login-form');
        const input = form.querySelector('.form-input');
        
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
    </script>
</body>
</html>