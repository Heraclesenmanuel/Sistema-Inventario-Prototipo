<?php
include_once './model/conexion.php';

function obtenerDatos(){
    // Valores por defecto
    $config = [
        'NombreApp' => 'APP',
        'Clave' => 'admin123',
        'precio_dollar' => '1.00'
    ];
    
    try {
        $conexion = new BaseDatos();
        $db = $conexion->conectar();

        $sql = "SELECT NombreAPP FROM `config_pag` LIMIT 1";
        $resul = @mysqli_query($db, $sql);

        if($resul && mysqli_num_rows($resul) > 0){
            if($row = mysqli_fetch_assoc($resul)){
                $config['NombreApp'] = $row['NombreAPP'];
                //Tal vez añadir más funcionalidad como colores y demás.
            }
            mysqli_free_result($resul);
        }
    } catch (Exception $e) {
        error_log("Error al obtener datos de configuración: " . $e->getMessage());
    }
    
    return $config;
}

$conf = obtenerDatos();
define('APP_NAME', $conf['NombreApp']);
define('APP_Date', date('d/m/y'));
define('APP_Password', $conf['Clave']);
define('APP_Logo', 'public/img/logo2.png');
?>