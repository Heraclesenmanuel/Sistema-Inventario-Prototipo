<?php
require_once 'config/config.php';

// Función para verificar si la base de datos existe
function verificarBaseDeDatosExiste() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "upel_inventario";

    try {
        $conn = new mysqli($servername, $username, $password);
        
        if ($conn->connect_error) {
            return false;
        }

        // Verificar si la base de datos existe
        $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
        $exists = $result->num_rows > 0;
        
        $conn->close();
        return $exists;

    } catch (Exception $e) {
        return false;
    }
}

// Función para crear la base de datos
function crearBaseDeDatos($datosIniciales) {
    // Configuración de la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "upel_inventario";

    try {
        // Crear conexión sin seleccionar base de datos
        $conn = new mysqli($servername, $username, $password);
        
        // Verificar conexión
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }

        // Crear base de datos
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql) === TRUE) {
            // Base de datos creada exitosamente
        } else {
            throw new Exception("Error creando base de datos: " . $conn->error);
        }

        // Seleccionar la base de datos
        $conn->select_db($dbname);

        // Array con todas las consultas SQL
        $queries = [
            "START TRANSACTION",
            "DROP TABLE IF EXISTS `config_pag`;",
            "CREATE TABLE `config_pag` (
                `id_config` int NOT NULL AUTO_INCREMENT,
                `NombreAPP` varchar(100) NOT NULL,
                PRIMARY KEY (`id_config`)
            )",
            "DROP TABLE IF EXISTS `director`;",
            "CREATE TABLE `director` (
                `ced_dir` VARCHAR(100) NOT NULL,
                `nombre` varchar(100) NOT NULL,
                `telf` VARCHAR(15) NOT NULL,
                PRIMARY KEY (`ced_dir`)
            )",
            "INSERT INTO `director` VALUES
                ('30000000', 'Ezequiel Angulo', '04245354900'), 
                ('31466704', 'Juanito Pulga', '04125555555'), 
                ('29543222', 'Adam Sandler', '04245432222'), 
                ('34566777', 'Jose Jose', '04124326789'), 
                ('25000623', 'Maria Jose', '04165348900'), 
                ('25000423', 'Magda Perozo', '04165348900')
                ",
            "DROP TABLE IF EXISTS `oficina`;", 
            "CREATE TABLE `oficina` (
                `num_oficina` VARCHAR(10) NOT NULL,
                `nombre` varchar(100) NOT NULL,
                `ced_dir` varchar(100) NOT NULL,
                `telefono` varchar(100) NOT NULL,
                PRIMARY KEY (`num_oficina`),
                CONSTRAINT `ced_dir_fk` FOREIGN KEY (`ced_dir`)
                REFERENCES `director`(`ced_dir`)
                ON UPDATE CASCADE
            )",
            "INSERT INTO `oficina` VALUES
                ('313', 'Biblioteca', '30000000', '04245354900'), 
                ('212', 'Informatica', '31466704', '04125555555'), 
                ('143', 'Cuentas', '29543222', '04245432222'), 
                ('204', 'Deportes', '34566777', '04124326789'), 
                ('305', 'Consejeria/Orientacion', '25000423', '04165348900'), 
                ('205', 'Servicios Generales', '25000423', '04165348900')
                ",
<<<<<<< HEAD
            // Tabla usuario
=======
            /* Tabla cuentascobrar
            "DROP TABLE IF EXISTS `cuentascobrar`;",
            "CREATE TABLE `cuentascobrar` (
                `id_historial` int NOT NULL AUTO_INCREMENT,
                `fecha` date NOT NULL,
                `cliente` varchar(100) NOT NULL,
                `tipo_pago` varchar(100) NOT NULL,
                `tipo_venta` varchar(100) NOT NULL,
                `total_usd` decimal(10,2) NOT NULL,
                `productos_vendidos` json NOT NULL,
                PRIMARY KEY (`id_historial`)
            )",

             Tabla historial
            "DROP TABLE IF EXISTS `historial`;",
            "CREATE TABLE `historial` (
                `id_historial` int NOT NULL AUTO_INCREMENT,
                `fecha` date NOT NULL,
                `cliente` varchar(100) NOT NULL,
                `tipo_pago` varchar(100) NOT NULL,
                `tipo_venta` varchar(100) NOT NULL,
                `total_usd` decimal(10,2) NOT NULL,
                `productos_vendidos` json NOT NULL,
                PRIMARY KEY (`id_historial`)
            )",*/
            "DROP TABLE IF EXISTS `rol_usuario`;",
            "CREATE TABLE `rol_usuario` (
                `id_cargo` int(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(100) NOT NULL,
                PRIMARY KEY(`id_cargo`)
            )",
            "INSERT INTO rol_usuario VALUES
            (1, 'Administrador'),
            (2, 'Usuario'),
            (3, 'Cuentas'),
            (4, 'Presupuesto')",
>>>>>>> d42897694361fe8c2147c1b73232393344293c4d
            "DROP TABLE IF EXISTS `usuario`;",
            "CREATE TABLE `usuario` (
                `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
                `cedula` varchar(100) NOT NULL,
                `clave` varchar(100) NOT NULL,
                `id_cargo` int NOT NULL,
                `correo` varchar(200) NOT NULL,
                `nombre` varchar(100) NOT NULL,
                PRIMARY KEY (`id_usuario`),
                CONSTRAINT `fk_usuario_cargo` FOREIGN KEY (`id_cargo`) 
                REFERENCES `rol_usuario`(`id_cargo`) ON UPDATE CASCADE
            )",
            "DROP TABLE IF EXISTS `usuario_super`;",
            "CREATE TABLE `usuario_super` (
                `id_usuario` int NOT NULL,
                `claveSuper` varchar(100) NOT NULL,
                PRIMARY KEY (`id_usuario`),
                CONSTRAINT `id_user_fk` FOREIGN KEY (`id_usuario`)
                REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE
            )",

            // Tabla proveedor
            "DROP TABLE IF EXISTS `proveedor`;",
            "CREATE TABLE `proveedor` (
                `rif` varchar(13) NOT NULL,
                `nombre` varchar(100) NOT NULL,
                `email` varchar(100) NOT NULL,
                `telefono` varchar(100) NOT NULL,
                `direccion` varchar(255) NOT NULL,
                `ced_encargado` varchar(12) NOT NULL,
                `estado` varchar(100) NOT NULL,
                `nota` varchar(100) NOT NULL,
                PRIMARY KEY (`rif`)
            )",
            "DROP TABLE IF EXISTS `tipo_prod`;",
            "CREATE TABLE `tipo_prod` (
                `id_tipo` INT(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(100) NOT NULL,
                `telf` VARCHAR(15) NOT NULL,
                PRIMARY KEY (`id_tipo`)
            )",
            "DROP TABLE IF EXISTS `servicio_proveedor`;",
            "CREATE TABLE `servicio_proveedor` (
                `id_tipo` INT(11) NOT NULL,
                `rif_prov` varchar(13) NOT NULL,
                PRIMARY KEY (`id_tipo`, `rif_prov`),
                CONSTRAINT `id_tipo_serv_fk` FOREIGN KEY (`id_tipo`)
                REFERENCES `tipo_prod`(`id_tipo`),
                CONSTRAINT `rif_prov_serv_fk` FOREIGN KEY (`rif_prov`)
                REFERENCES `proveedor`(`rif`)
                ON UPDATE CASCADE
            )",
            "DROP TABLE IF EXISTS `producto`;",
            "CREATE TABLE `producto` (
                `id_producto` int NOT NULL AUTO_INCREMENT,
                `nombre` varchar(100) NOT NULL,
                `un_disponibles` int DEFAULT 0,
                `medida` varchar(100) DEFAULT NULL,
                `id_tipo` INT(11) DEFAULT NULL,
                `fecha_r` DATE NOT NULL,
                PRIMARY KEY (`id_producto`),
                CONSTRAINT `id_tipo_fk` FOREIGN KEY (`id_tipo`)
                REFERENCES `tipo_prod`(`id_tipo`)
            )",
            "DROP TABLE IF EXISTS `solicitud`;",
            "CREATE TABLE `solicitud` (
                `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
                `id_solicitante` int(11) NOT NULL,
                `fecha_solic` DATE NOT NULL,
                `fecha_deseo` DATE NOT NULL,
                `comentarios` VARCHAR(500) NOT NULL,
                `num_oficina` VARCHAR(10),
                `estado` VARCHAR(20) DEFAULT 'Pendiente',
                `apelada` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id_solicitud`),
                CONSTRAINT `num_oficina_solic_fk` FOREIGN KEY (`num_oficina`)
                REFERENCES `oficina`(`num_oficina`)
                ON UPDATE CASCADE
            )",
            "DROP TABLE IF EXISTS `prod_solic`;",
            "CREATE TABLE `prod_solic` (
                `id_solicitud` int(11) NOT NULL, 
                `num_linea` int(11) NOT NULL, 
                `nombre` varchar(100) NOT NULL, 
                `un_deseadas` int DEFAULT 0, 
                `medida` varchar(100) DEFAULT NULL, 
                `id_tipo` INT(11) DEFAULT NULL, 
                PRIMARY KEY (`id_solicitud`, `num_linea`),
                CONSTRAINT `id_solic_fk` FOREIGN KEY (`id_solicitud`) 
                REFERENCES solicitud(id_solicitud),
                CONSTRAINT `id_tipo_solic_fk` FOREIGN KEY (`id_tipo`)
                REFERENCES `tipo_prod`(`id_tipo`)
            );",
            "DROP TABLE IF EXISTS `notificacion`;",
            "CREATE TABLE `notificacion` (
                `id_notif` int(11) NOT NULL AUTO_INCREMENT,
                `tipo` int(11) NOT NULL,
                `fecha_notif` DATE NOT NULL,
                `leido` tinyint(1) DEFAULT 0,
                `id_usuario` int(11) NOT NULL,
                `num_oficina` VARCHAR(10), 
                PRIMARY KEY (`id_notif`),
                CONSTRAINT `num__ofic_notifk` FOREIGN KEY (`num_oficina`) 
                REFERENCES `oficina`(`num_oficina`),
                CONSTRAINT `id_usuario_fk` FOREIGN KEY (`id_usuario`) 
                REFERENCES `usuario`(`id_usuario`)
            )",
            "CREATE TABLE prov_recomendaciones (
                rif_proveedor VARCHAR(13),
                id_tipo INT(11),
                PRIMARY KEY (rif_proveedor, id_tipo),
                CONSTRAINT fk_prov FOREIGN KEY (rif_proveedor) REFERENCES proveedor(rif),
                CONSTRAINT fk_tipo FOREIGN KEY (id_tipo) REFERENCES tipo_prod(id_tipo)
            );",
            "DROP TABLE IF EXISTS `codigo_recuperacion`;",
            "CREATE TABLE `codigo_recuperacion` (
                `id_usuario` int(11) NOT NULL,
                `codigo` varchar(100) NOT NULL,
                PRIMARY KEY (`id_usuario`)
            )",
            "DROP TABLE IF EXISTS `ofic_usuario`;",
            "CREATE TABLE `ofic_usuario` (
            num_oficina VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            id_usuario INT(11) NOT NULL,
            PRIMARY KEY (num_oficina, id_usuario),
            CONSTRAINT num_oficina_fk FOREIGN KEY (num_oficina) 
                REFERENCES oficina(num_oficina)
                ON UPDATE CASCADE ON DELETE CASCADE,
            CONSTRAINT id_usuario_ofic_fk FOREIGN KEY (id_usuario) 
                REFERENCES usuario(id_usuario)
                ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB;",
            "COMMIT;"
        ];
        // Ejecutar consultas de creación de tablas
        foreach ($queries as $query) {
            if ($conn->query($query) === FALSE) {
                throw new Exception("Error ejecutando consulta: " . $conn->error . "<br>Consulta: " . $query);
            }
        }
        // Insertar datos en admin con la clave proporcionada
        $claveSuper = $conn->real_escape_string($datosIniciales['claveSuper']);
        $conn->query("INSERT INTO `config_pag` (`id_config`, `NombreAPP`) VALUES (1, 'App')");

        // Insertar usuario con los datos proporcionados
        $cedula = $conn->real_escape_string($datosIniciales['cedula']);
        $clave = $conn->real_escape_string($datosIniciales['clave']);
        $id_cargo = (int)$datosIniciales['id_cargo'];
        $nombre = $conn->real_escape_string($datosIniciales['nombre']);
        $correo = strtolower($conn->real_escape_string($datosIniciales['correo']));
        $conn->query("INSERT INTO `usuario` (`id_usuario`, `cedula`, `clave`, `id_cargo`, `nombre`, `correo`)
                            VALUES (1, '$cedula', '$clave', $id_cargo, '$nombre', '$correo')");
        $conn->query("INSERT INTO `usuario_super` (`id_usuario`, `claveSuper`)
                        VALUES (1, '$claveSuper')");
        if (!empty($datosIniciales['oficinas_seleccionadas'])) {
            $oficinasSeleccionadas = json_decode($datosIniciales['oficinas_seleccionadas'], true);
            
            if (is_array($oficinasSeleccionadas) && !empty($oficinasSeleccionadas)) {
                foreach ($oficinasSeleccionadas as $oficinaCod) 
                {
                    $oficinaCod = $conn->real_escape_string($oficinaCod);
                    $conn->query("INSERT INTO `ofic_usuario` (`num_oficina`, `id_usuario`) 
                                     VALUES ('$oficinaCod', 1)");
                }
            }
        }
        return true;

    } catch (Exception $e) {
        error_log("Error creando base de datos: " . $e->getMessage());
        return false;
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
}

// Variables para mensajes
$errorBD = '';
$erroresValidacion = [];
$mostrarExito = false;

// Verificar si estamos en proceso de creación de base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_bd'])) {
    // Validar datos del formulario
    $datosIniciales = [
        'claveSuper' => trim($_POST['claveSuper'] ?? ''),
        'cedula' => trim($_POST['cedula'] ?? ''),
        'clave' => trim($_POST['clave'] ?? ''),
        'id_cargo' => 1, // Valor por defecto
        'nombre' => trim($_POST['nombre'] ?? ''),
        'correo' => trim($_POST['correo'] ?? ''),
        'oficinas_seleccionadas' => $_POST['oficinas_seleccionadas'] ?? ''
    ];

    // Validaciones
    if (empty($datosIniciales['claveSuper'])) {
        $erroresValidacion[] = "La clave super es obligatoria";
    } elseif (strlen($datosIniciales['claveSuper']) < 8) {
        $erroresValidacion[] = "La clave super debe tener al menos 8 caracteres";
    }

    if (empty($datosIniciales['cedula'])) {
        $erroresValidacion[] = "La cédula es obligatoria";
    }

    if (empty($datosIniciales['clave'])) {
        $erroresValidacion[] = "La clave de usuario es obligatoria";
    } elseif (strlen($datosIniciales['clave']) < 6) {
        $erroresValidacion[] = "La clave de usuario debe tener al menos 6 caracteres";
    }

    if (empty($datosIniciales['nombre'])) {
        $erroresValidacion[] = "El nombre es obligatorio";
    }

    // Si no hay errores, crear la base de datos
    if (empty($erroresValidacion)) {
        if (crearBaseDeDatos($datosIniciales)) {
            $mostrarExito = true;
        } else {
            $errorBD = "Error al crear la base de datos. Verifica los logs para más información.";
        }
    }
}

// Verificar si la base de datos existe
if (!verificarBaseDeDatosExiste()) {
    // Mostrar formulario de creación de base de datos
?>
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
<?php
    exit();
}

// Si la base de datos existe, continuar con el enrutamiento normal

// autoload
spl_autoload_register(function($className) {
    $paths = [
        'controllers/' . $className . '.php',
        'models/' . $className . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Función auxiliar para detectar peticiones AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ||
            (isset($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
}

// Función para enviar error JSON
function sendJsonError($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// Enrutamiento 
$action = $_GET['action'] ?? 'Inicio';
$actionName = ucfirst($action) . 'Controller';
$method = $_GET['method'] ?? 'inicio';
$controllerFile = 'controllers/' . $actionName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // verificación de las clases
    if (!class_exists($actionName)) {
        if (isAjaxRequest()) {
            sendJsonError("La clase $actionName no está definida");
        }
        die("Error: La clase $actionName no está definida en $controllerFile");
    }
    
    $controller = new $actionName();
    
    // verificación del método
    if (!method_exists($controller, $method)) {
        if (isAjaxRequest()) {
            sendJsonError("El método $method() no existe en $actionName");
        }
        die("Error: El método $method() no existe en $actionName");
    }
    
    $controller->$method();
} else {
    error_log("Controlador no encontrado: $controllerFile");
    
    if (isAjaxRequest()) {
        sendJsonError("Controlador no encontrado", 404);
    }
    
    require_once 'views/error/404.php';
}