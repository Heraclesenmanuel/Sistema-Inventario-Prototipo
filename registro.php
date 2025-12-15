<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Inicial - Sistema de Inventario UPEL</title>
    <link rel="shortcut icon" href="<?= APP_Logo ?>" type="image/x-icon">
    <link rel="stylesheet" href="public/css/first_config.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container animate__animated animate__fadeIn">
        <div class="header">
            <div class="logo-container">
                <i class="fas fa-warehouse"></i>
            </div>
            <h1>Configuración Inicial</h1>
            <p class="subtitle">Configure los datos de acceso para su sistema de gestión de inventario</p>
        </div>

        <div class="info-box animate__animated animate__fadeInUp">
            <div class="info-box-title">
                <i class="fas fa-info-circle"></i>
                Información Importante
            </div>
            <div class="info-box-text">
                Este proceso creará la base de datos "upel_inventario" con todas las tablas necesarias. 
                Los datos que ingrese serán sus credenciales de acceso al sistema.
            </div>
        </div>

        <form method="POST" id="setupForm">
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-shield-alt"></i>
                    Clave de Seguridad
                </div>
                
                <div class="form-group">
                    <label for="claveSuper">
                        Clave Super <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="claveSuper" 
                        name="claveSuper" 
                        placeholder="Ingrese la clave super"
                        value="<?= htmlspecialchars($_POST['claveSuper'] ?? '') ?>"
                        required
                        minlength="8"
                    >
                    <div class="help-text">Mínimo 8 caracteres. Clave para funciones administrativas críticas.</div>
                </div>
            </div>

            <div class="divider"></div>
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user-shield"></i>
                    Usuario Administrador
                </div>
                
                <div class="form-group">
                    <label for="nombre">
                        Nombre Completo <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Juan Pérez"
                        value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="cedula">
                        Usuario / Cédula <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="cedula" 
                        name="cedula" 
                        placeholder="admin o V12345678"
                        value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>"
                        required
                    >
                    <div class="help-text">Este será su nombre de usuario para iniciar sesión.</div>
                </div>
                <div class="oficinas-grid">
                    <div class="oficina-item">
                        <input type="checkbox" id="cat_Biblioteca" name="categorias[]" value="313">
                        <label for="cat_Biblioteca">Biblioteca</label>
                        <input type="checkbox" id="cat_Informatica" name="categorias[]" value="212">
                        <label for="cat_Informatica">Informatica</label>
                        <input type="checkbox" id="cat_Cuentas" name="categorias[]" value="143">
                        <label for="cat_Cuentas">Cuentas</label>
                        <input type="checkbox" id="cat_Deportes" name="categorias[]" value="204">
                        <label for="cat_Deportes">Deportes</label>
                        <input type="checkbox" id="cat_Consejeria/Orientacion" name="categorias[]" value="305">
                        <label for="cat_Consejeria/Orientacion">Consejeria/Orientacion</label>
                        <input type="checkbox" id="cat_Servicios Generales" name="categorias[]" value="205">
                        <label for="cat_Servicios Generales">Servicios Generales</label>
                    </div>
                </div>
                <input type="hidden" name="oficinas_seleccionadas" id="oficinasSeleccionadasInput">
                <div class="form-group">
                    <label for="correo">
                        Correo electrónico <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="correo" 
                        name="correo" 
                        placeholder="tucorreoelectronico@gmail.com"
                        value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                        required
                    >
                    <div class="help-text">Este será su correo para comunicados fuera del sistéma.</div>
                </div>

                <div class="form-group">
                    <label for="clave">
                        Contraseña <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="clave" 
                        name="clave" 
                        placeholder="Ingrese su contraseña"
                        required
                        minlength="6"
                    >
                    <div class="help-text">Mínimo 6 caracteres. Contraseña de acceso al sistema.</div>
                </div>
            </div>

            <input type="hidden" name="id_cargo" value="1">
            
            <button type="submit" name="crear_bd" class="btn-primary">
                <i class="fas fa-database"></i>
                Crear Base de Datos
            </button>
        </form>
    </div>

    <script>
        
        <?php if ($mostrarExito): ?>
        Swal.fire({
            icon: 'success',
            title: '¡Base de Datos Creada!',
            text: 'La configuración se completó exitosamente. Redirigiendo al sistema...',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            iconColor: '#22c55e',
            customClass: {
                popup: 'animate__animated animate__fadeInDown'
            }
        }).then(() => {
            window.location.href = '<?= $_SERVER['PHP_SELF'] ?>';
        });
        <?php endif; ?>

        <?php if (!empty($erroresValidacion)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Errores de Validación',
            html: '<div style="text-align: left;"><ul style="margin: 0; padding-left: 25px; line-height: 1.8;">' +
                <?php 
                $erroresHTML = '';
                foreach ($erroresValidacion as $error) {
                    $erroresHTML .= '<li style="margin-bottom: 8px;">' . htmlspecialchars($error) . '</li>';
                }
                echo json_encode($erroresHTML);
                ?> +
                '</ul></div>',
            confirmButtonColor: '#22c55e',
            confirmButtonText: '<i class="fas fa-check"></i> Entendido',
            customClass: {
                popup: 'animate__animated animate__shakeX'
            }
        });
        <?php endif; ?>

        <?php if (!empty($errorBD)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error al Crear Base de Datos',
            text: '<?= htmlspecialchars($errorBD) ?>',
            confirmButtonColor: '#22c55e',
            confirmButtonText: '<i class="fas fa-redo"></i> Reintentar',
            customClass: {
                popup: 'animate__animated animate__shakeX'
            }
        });
        <?php endif; ?>

        // Validación en tiempo real
        document.getElementById('setupForm').addEventListener('submit', function(e) {
            const claveSuper = document.getElementById('claveSuper').value;
            const clave = document.getElementById('clave').value;
            const categoriasSeleccionadas = [];
                const checkboxes = document.querySelectorAll('input[name="categorias[]"]:checked');
                checkboxes.forEach(checkbox => {
                    categoriasSeleccionadas.push(checkbox.value);
                });
            
            console.log('Categorías seleccionadas:', categoriasSeleccionadas);
            
            if (claveSuper.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Clave Super Inválida',
                    text: 'La clave super debe tener al menos 8 caracteres',
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                    customClass: {
                        popup: 'animate__animated animate__headShake'
                    }
                });
                return false;
            }
            
            if (clave.length < 6) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña Inválida',
                    text: 'La contraseña debe tener al menos 6 caracteres',
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                    customClass: {
                        popup: 'animate__animated animate__headShake'
                    }
                });
                return false;
            }
            else
            {
                const oficinasSeleccionadasInput = document.getElementById('oficinasSeleccionadasInput');
                oficinasSeleccionadasInput.value = JSON.stringify(categoriasSeleccionadas);
                console.log('Oficinas a enviar:', oficinasSeleccionadasInput.value);
            }
        });
        async function agregarOficinas(oficinasSeleccionadas)
        {
            const formData = new FormData();
            formData.append(oficinasSeleccionadas);

        }
    </script>
</body>
</html>