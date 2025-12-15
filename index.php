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

        // Leer y ejecutar el archivo SQL
        $sqlFile = 'upel_inventario.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("El archivo SQL de instalación no se encuentra: $sqlFile");
        }

        $sqlContent = file_get_contents($sqlFile);
        
        // Ejecutar multi-query
        if ($conn->multi_query($sqlContent)) {
            // Consumir todos los resultados del multi_query para liberar la conexión
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        } else {
             throw new Exception("Error importando base de datos: " . $conn->error);
        }

        // Insertar datos en admin con la clave proporcionada
        // NOTA: El dump ya trae un usuario ID 1. Lo actualizamos con los datos del formulario.
        
        $claveSuper = $conn->real_escape_string($datosIniciales['claveSuper']);
        $cedula = $conn->real_escape_string($datosIniciales['cedula']);
        $clave = $conn->real_escape_string($datosIniciales['clave']);
        $id_cargo = (int)$datosIniciales['id_cargo'];
        $nombre = $conn->real_escape_string($datosIniciales['nombre']);
        $correo = strtolower($conn->real_escape_string($datosIniciales['correo']));

        // Actualizar o Insertar Usuario ID 1
        $checkUser = $conn->query("SELECT id_usuario FROM usuario WHERE id_usuario = 1");
        if ($checkUser->num_rows > 0) {
            // Actualizar
            $sqlUser = "UPDATE usuario SET 
                        cedula = '$cedula', 
                        clave = '$clave', 
                        id_cargo = $id_cargo, 
                        nombre = '$nombre', 
                        correo = '$correo' 
                        WHERE id_usuario = 1";
            
            if (!$conn->query($sqlUser)) {
                throw new Exception("Error actualizando usuario admin: " . $conn->error);
            }

            // Actualizar Super Usuario
            $checkSuper = $conn->query("SELECT id_usuario FROM usuario_super WHERE id_usuario = 1");
            if ($checkSuper->num_rows > 0) {
                $conn->query("UPDATE usuario_super SET claveSuper = '$claveSuper' WHERE id_usuario = 1");
            } else {
                $conn->query("INSERT INTO usuario_super (id_usuario, claveSuper) VALUES (1, '$claveSuper')");
            }
        } else {
            // Insertar si no existe (raro si viene del dump, pero por seguridad)
            $conn->query("INSERT INTO usuario (id_usuario, cedula, clave, id_cargo, nombre, correo)
                        VALUES (1, '$cedula', '$clave', $id_cargo, '$nombre', '$correo')");
            $conn->query("INSERT INTO usuario_super (id_usuario, claveSuper) VALUES (1, '$claveSuper')");
        }

        // Manejar oficinas
        // Primero limpiamos las oficinas del usuario 1 para re-asignar las seleccionadas
        $conn->query("DELETE FROM ofic_usuario WHERE id_usuario = 1");

        if (!empty($datosIniciales['oficinas_seleccionadas'])) {
            $oficinasSeleccionadas = json_decode($datosIniciales['oficinas_seleccionadas'], true);
            
            if (is_array($oficinasSeleccionadas) && !empty($oficinasSeleccionadas)) {
                foreach ($oficinasSeleccionadas as $oficinaCod) 
                {
                    $oficinaCod = $conn->real_escape_string($oficinaCod);
                    $conn->query("INSERT INTO ofic_usuario (num_oficina, id_usuario) 
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
if (!verificarBaseDeDatosExiste() || $mostrarExito) {
    include 'registro.php'; 
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