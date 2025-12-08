<?php
include_once './model/conexion.php';

function obtenerName(){
    $config = [
        'NombreApp' => 'APP'
    ];
    
    try {
        $conexion = new BaseDatos();
        $db = $conexion->conectar();
        
        if (!$db || $db->connect_error) {
            throw new Exception("Error de conexión a la base de datos");
        }
        
        $sql = "SELECT NombreAPP FROM `config_pag` LIMIT 1";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result && $result->num_rows > 0){
                $row = $result->fetch_assoc();
                $config['NombreApp'] = $row['NombreAPP'];
            }
            $stmt->close();
        }
        $db->close();
        
    } catch (Exception $e) {
        error_log("Error al obtener datos de configuración: " . $e->getMessage());
    }
    
    return $config;
}

function obtenerPass(){
    $pass = [
        'claveSuper' => ''
    ];
    
    // Verificar si la sesión tiene id_usuario
    if (!isset($_SESSION['id_usuario'])) {
        error_log("Error: Sesión no iniciada o id_usuario no establecido");
        return $pass;
    }
    
    try {
        $conexion = new BaseDatos();
        $db = $conexion->conectar();
        
        if (!$db || $db->connect_error) {
            throw new Exception("Error de conexión a la base de datos");
        }
        
        $id = $_SESSION['id_usuario'];
        $sql = "SELECT claveSuper FROM usuario_super WHERE id_usuario = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result && $result->num_rows > 0){
                $row = $result->fetch_assoc();
                $pass['claveSuper'] = $row['claveSuper'];
            } else {
                error_log("No se encontró usuario_super con id_usuario: " . $id);
            }
            $stmt->close();
        } else {
            error_log("Error al preparar consulta: " . $db->error);
        }
        $db->close();
        
    } catch (Exception $e) {
        error_log("Error al obtener clave super: " . $e->getMessage());
    }
    
    return $pass;
}

$conf = obtenerName();
$pass = obtenerPass();

// Definir constantes con valores predeterminados si no hay datos
define('APP_NAME', isset($conf['NombreApp']) ? $conf['NombreApp'] : 'APP');
define('APP_Date', date('d/m/Y'));
define('APP_Logo', 'public/img/logo2.png');
define('APP_Password', isset($pass['claveSuper']) ? $pass['claveSuper'] : '');

?>