<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Inventario UPEL - Cambiar Contraseña">
    <meta name="theme-color" content="#3F51B5">
    <title><?= APP_NAME ?> - Cambiar Contraseña</title>
    
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
                    <i data-lucide="lock-keyhole" class="icon-logo"></i>
                </div>
                <h1 class="login-title">Nueva Contraseña</h1>
                <p class="login-subtitle">Crea una contraseña segura para tu cuenta</p>
            </header>
            
            <!-- Formulario de Cambio de Contraseña -->
            <form action="?action=inicio&method=submitNewPassw" method="post" class="login-form" novalidate>
                
                <!-- Alertas -->
                <?php if (isset($_GET['error']) && $_GET['error']==1): ?>
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="alert-triangle" class="alert-icon"></i>
                        <div class="alert-content">
                            <strong class="alert-title">Las contraseñas no coinciden</strong>
                            <p class="alert-message">Por favor verifica que ambas contraseñas sean idénticas</p>
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
                
                <!-- Campo de Nueva Contraseña -->
                <div class="form-group">
                    <label for="passw" class="form-label">
                        <i data-lucide="lock" class="label-icon"></i>
                        <span>Nueva Contraseña</span>
                    </label>
                    <div class="input-wrapper password-wrapper">
                        <i data-lucide="key" class="input-icon"></i>
                        <input 
                            type="password" 
                            id="passw" 
                            name="passw" 
                            class="form-input"
                            placeholder="Mínimo 8 caracteres" 
                            required
                            minlength="8"
                            autocomplete="new-password"
                            aria-label="Nueva contraseña"
                            aria-required="true"
                        >
                        <button 
                            type="button" 
                            class="btn-toggle" 
                            onclick="togglePassword('passw', 'toggleIcon1')"
                            aria-label="Mostrar u ocultar contraseña"
                        >
                            <i data-lucide="eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small class="strength-text" id="strengthText">Ingresa una contraseña</small>
                    </div>
                </div>
            
                <!-- Campo de Confirmar Contraseña -->
                <div class="form-group">
                    <label for="passw2" class="form-label">
                        <i data-lucide="lock-keyhole" class="label-icon"></i>
                        <span>Confirmar Contraseña</span>
                    </label>
                    <div class="input-wrapper password-wrapper">
                        <i data-lucide="shield-check" class="input-icon"></i>
                        <input 
                            type="password" 
                            id="passw2" 
                            name="passw2" 
                            class="form-input"
                            placeholder="Repite la contraseña" 
                            required
                            minlength="8"
                            autocomplete="new-password"
                            aria-label="Confirmar contraseña"
                            aria-required="true"
                        >
                        <button 
                            type="button" 
                            class="btn-toggle" 
                            onclick="togglePassword('passw2', 'toggleIcon2')"
                            aria-label="Mostrar u ocultar contraseña"
                        >
                            <i data-lucide="eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                    <small class="form-hint" id="matchHint"></small>
                </div>
                
                <!-- Botón de Submit -->
                <button type="submit" name="init" class="btn-submit" id="submitBtn" disabled>
                    <i data-lucide="check-circle-2" class="btn-icon"></i>
                    <span>Cambiar Contraseña</span>
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
        
        // Toggle Password Visibility
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }
            
            lucide.createIcons();
        }
        
        // Password Strength Checker
        const passwInput = document.getElementById('passw');
        const passw2Input = document.getElementById('passw2');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const matchHint = document.getElementById('matchHint');
        const submitBtn = document.getElementById('submitBtn');
        
        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 10;
            
            return Math.min(strength, 100);
        }
        
        passwInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            strengthFill.style.width = strength + '%';
            
            if (strength === 0) {
                strengthFill.style.backgroundColor = '#E8EAF0';
                strengthText.textContent = 'Ingresa una contraseña';
                strengthText.style.color = '#5F6368';
            } else if (strength < 50) {
                strengthFill.style.backgroundColor = '#E44336';
                strengthText.textContent = 'Contraseña débil';
                strengthText.style.color = '#E44336';
            } else if (strength < 75) {
                strengthFill.style.backgroundColor = '#FFC107';
                strengthText.textContent = 'Contraseña media';
                strengthText.style.color = '#FFC107';
            } else {
                strengthFill.style.backgroundColor = '#2ECC71';
                strengthText.textContent = 'Contraseña fuerte';
                strengthText.style.color = '#2ECC71';
            }
            
            checkPasswordMatch();
        });
        
        passw2Input.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const passw = passwInput.value;
            const passw2 = passw2Input.value;
            
            if (passw2.length === 0) {
                matchHint.textContent = '';
                submitBtn.disabled = true;
                return;
            }
            
            if (passw === passw2 && passw.length >= 8) {
                matchHint.textContent = '✓ Las contraseñas coinciden';
                matchHint.style.color = '#2ECC71';
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            } else {
                matchHint.textContent = '✗ Las contraseñas no coinciden';
                matchHint.style.color = '#E44336';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
            }
        }
        
        // Form Validation Enhancement
        const inputs = document.querySelectorAll('.form-input');
        
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