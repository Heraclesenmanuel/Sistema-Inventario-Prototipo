<?php
require_once 'model/base.php';
class Notificaciones extends Base{
    public function obtenerDatos(){
        $sql = 'SELECT n.* 
                FROM receptor_notif rn
                INNER JOIN notificacion n ON rn.id_notif=n.id_notif
                WHERE rn.id_usuario = ?';
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
        try
        {
            $stmt->bind_param('i', $_SESSION['id']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if(!$resultado) {
                return [];
            }
            $datos = [];
            while($row = $resultado->fetch_assoc()) {
                $datos[] = $row;
            }
            return $datos;
        } 
        finally 
        {
            $stmt->close();
        }
    }
    public function obtenerNoLeidas(){
        $stmt = $this->db->prepare('SELECT COUNT(leido) FROM receptor_notif WHERE leido=1 AND id_usuario = ?');
        if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
        try
        {
            $stmt->bind_param('i', $_SESSION['id']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if(!$resultado) {
                return 0;
            }
            $datos = $resultado->fetch_row();
            return $datos[0];
        }
        finally 
        {
            $stmt->close();
        }
    }
    public function validarTiposDatos($datos, $stmt)
    {
        $codigo = $datos['codigo'];
        $nombre = $datos['nombre'];
        $un_disponibles = (int)$datos['un_disponibles'];
        $tipo_p = $datos['tipo_p'];
        $medida = $datos['medida'];

        $stmt->bind_param('ssisi', $codigo, $nombre, $un_disponibles, $medida, $tipo_p);
        
        return $stmt;
    }
    public function guardarDatos($datos) {
        $stmt = $this->db->prepare("INSERT INTO notificacion (codigo, nombre, un_disponibles, medida, id_tipo, fecha_r) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            $this->db->close();
            die('Error en la preparación de la consulta SQL');
        }
        $result = $this->validarTiposDatos($datos, $stmt)->execute();
        if (!$result) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
        }
        $stmt->close();
        
        return $result;
    }
        public function eliminarDatos($id) {
            $stmt = $this->db->prepare("DELETE FROM notificacion WHERE id_producto = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            try {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    throw new Exception("No se encontró el producto con ID $id");
                }
            } finally {
                $stmt->close();
            }
        }
    public function obtenerProductoPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM notificacion WHERE id_producto = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function actualizarProducto($datos) {
        $stmt = $this->db->prepare("UPDATE notificacion SET 
            codigo = ?, 
            nombre = ?, 
            medida = ?, 
            un_disponibles = ?, 
            id_tipo = ?,
            WHERE id_producto = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("sssis", 
            $datos['codigo'],
            $datos['nombre'],
            $datos['medida'],
            $datos['un_disponibles'],
            $datos['id_producto']
        );
        
        return $stmt->execute();
    }
    public function obtenerEstadisticas() {
        // Estadísticas por estado (para gráfico de torta)
        $sqlEstados = "SELECT 
                        nombre, 
                        SUM(un_disponibles) as cantidad,
                        ROUND(SUM(un_disponibles) * 100.0 / (SELECT SUM(un_disponibles) FROM producto), 2) as porcentaje
                        FROM notificacion
                        GROUP BY nombre";
        
        $resultEstados = $this->db->query($sqlEstados);
        
        if (!$resultEstados) {
            throw new Exception("Error al obtener estadísticas por estado: " . $this->db->error);
        }
        
        // Estadísticas mensuales (para tabla)
        $sqlMensual = "SELECT * FROM usuario";
        
        $resultMensual = $this->db->query($sqlMensual);
        
        if (!$resultMensual) {
            throw new Exception("Error al obtener estadísticas mensuales: " . $this->db->error);
        }
        
        return [
            'por_estado' => $resultEstados->fetch_all(MYSQLI_ASSOC),
            'mensuales' => $resultMensual->fetch_all(MYSQLI_ASSOC)
        ];
    }
}
?>