<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Inventario UPEL - Confirmar Código">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?> - Confirmar Código</title>
    
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
                <h1 class="login-title">Verificación de Código</h1>
                <p class="login-subtitle">Ingresa el código enviado a tu correo</p>
            </header>
            
            <!-- Formulario de Verificación -->
            <form action="?action=inicio&method=checkCode" method="post" class="login-form" novalidate>
                
                <!-- Alertas -->
                <?php if (isset($_GET['error']) && $_GET['error']==1): ?>
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="x-circle" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">Código incorrecto</strong>
                            <p class="alert-message">El código ingresado no es válido. Por favor verifica e intenta nuevamente</p>
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
                <?php elseif (isset($_GET['msg'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i data-lucide="mail-check" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">Código enviado</strong>
                            <p class="alert-message">Te hemos enviado el código a tu correo. Por favor revísalo e ingrésalo aquí</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Campo de Código -->
                <div class="form-group">
                    <label for="codigo" class="form-label">
                        <i data-lucide="hash" class="label-icon"></i>
                        <span>Código de Verificación</span>
                    </label>
                    <div class="input-wrapper">
                        <i data-lucide="key-round" class="input-icon"></i>
                        <input 
                            type="text" 
                            id="codigo" 
                            name="codigo" 
                            class="form-input code-input"
                            placeholder="Ingresa el código de 32 caracteres" 
                            required
                            minlength="32"
                            maxlength="32"
                            autocomplete="one-time-code"
                            aria-label="Código de verificación"
                            aria-required="true"
                            spellcheck="false"
                        >
                    </div>
                    <small class="form-hint">El código tiene 32 caracteres y fue enviado a tu correo electrónico</small>
                </div>
                
                <!-- Botón de Submit -->
                <button type="submit" name="init" class="btn-submit">
                    <i data-lucide="check-circle" class="btn-icon"></i>
                    <span>Verificar Código</span>
                </button>
            </form>
            
            <!-- Footer con Links -->
            <footer class="footer-links">
                <a href="?action=inicio&method=forgotPassw" class="link-forgot">
                    <i data-lucide="rotate-ccw" class="link-icon"></i>
                    <span>Reenviar código</span>
                </a>
                <span class="link-separator">|</span>
                <a href="?action=inicio&method=login" class="link-forgot">
                    <i data-lucide="arrow-left" class="link-icon"></i>
                    <span>Volver al inicio</span>
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
            
            // Auto-format code (uppercase, remove spaces)
            this.value = this.value.toUpperCase().replace(/\s/g, '');
        });
        
        // Character counter
        input.addEventListener('input', function() {
            const length = this.value.length;
            const hint = this.parentElement.nextElementSibling;
            
            if (length > 0) {
                hint.textContent = `${length}/32 caracteres ingresados`;
                
                if (length === 32) {
                    hint.style.color = '#2ECC71';
                    hint.textContent = '✓ Código completo';
                } else {
                    hint.style.color = '#5F6368';
                }
            } else {
                hint.textContent = 'El código tiene 32 caracteres y fue enviado a tu correo electrónico';
                hint.style.color = '#5F6368';
            }
        });
    </script>
</body>
</html>