<?php
require_once 'model/base.php';
class Inventario extends Base {

    public function obtenerDatos(){
        $sql = 'SELECT p.*, tp.nombre as tipo, SUM(rp.un_anadidas) as un_disponibles
                FROM producto p 
                INNER JOIN tipo_prod tp ON p.id_tipo=tp.id_tipo
                INNER JOIN prod_solic ps ON p.id_producto=ps.id_producto
                INNER JOIN registro_prod rp ON ps.id_solicitud=rp.id_solicitud AND ps.num_linea=rp.num_linea
                WHERE valido=1
                GROUP BY p.nombre';
        $resultado = $this->db->query($sql);

        if(!$resultado) {
            return [];
        }

        $datos = [];
        while($row = $resultado->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    public function getTiposCompleto()
    {
        $sql = "SELECT tp.id_tipo as id_tipo, tp.nombre as nombre, SUM(un_deseadas) as cant_pend, 
                SUM(un_deseadas) as cant_solic 
                FROM tipo_prod tp 
                INNER JOIN producto p ON p.id_tipo=tp.id_tipo
                LEFT JOIN prod_solic ps ON p.id_producto=ps.id_producto
                GROUP BY tp.id_tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $tipos = [];

        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }

        return [
            'data' => $tipos,
            'success' => true
        ];
    }
    public function validarTiposDatos($datos, $stmt)
    {
        $nombre = $datos['nombre'];
        $tipo_p = $datos['tipo_p'];
        $medida = $datos['medida'];

        $stmt->bind_param('ssi', $nombre, $medida, $tipo_p);
        
        return $stmt;
    }
    public function guardarDatos($datos) {
        $stmt = $this->db->prepare("INSERT INTO producto (nombre, medida, id_tipo, fecha_r) VALUES (?, ?, ?, NOW())");
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
            $stmt = $this->db->prepare("DELETE FROM producto WHERE id_producto = ?");
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
        $stmt = $this->db->prepare("SELECT * FROM producto WHERE id_producto = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function actualizarProducto($datos) {
        $stmt = $this->db->prepare("UPDATE producto SET  
            nombre = ?, 
            medida = ?, 
            id_tipo = ?
            WHERE id_producto = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("ssii", 
            $datos['nombre'],
            $datos['medida'],
            $datos['tipo_p'],
            $datos['id_producto']
        );
        
        return $stmt->execute();
    }
    public function guardarCategoria($datos) {
        $stmt = $this->db->prepare("INSERT INTO tipo_prod (nombre) VALUES (?)");
        if (!$stmt) {
            $this->db->close();
            die('Error en la preparación de la consulta SQL');
        }
        $stmt->bind_param('s', $datos['nombre']);
        $stmt->execute();
        if (!$stmt) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
        }
        $stmt->close();
        
        return $stmt;
    }
    public function obtenerCategoriaPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM tipo_prod WHERE id_tipo = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function eliminarCategoria($id) {
        $stmt = $this->db->prepare("DELETE FROM tipo_prod WHERE id_tipo = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        try {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return true;
            } else {
                throw new Exception("No se encontró la categoria con ID $id");
            }
        } finally {
            $stmt->close();
        }
    }
    public function actualizarCategoria($nombre, $id) 
    {
        $stmt = $this->db->prepare("UPDATE tipo_prod SET  
            nombre = ?
            WHERE id_tipo= ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("si", $nombre, $id);
        return $stmt->execute();
    }
    public function obtenerEstadisticas() {
        // Estadísticas por estado (para gráfico de torta)
        // Estadísticas por estado (para gráfico de torta)
        // Fixed: Handle division by zero if total stock is 0
        $sqlEstados = "SELECT 
                        nombre, 
                        SUM(un_disponibles) as cantidad,
                        ROUND(
                            CASE 
                                WHEN (SELECT SUM(un_disponibles) FROM producto) > 0 
                                THEN SUM(un_disponibles) * 100.0 / (SELECT SUM(un_disponibles) FROM producto)
                                ELSE 0 
                            END, 
                        2) as porcentaje
                        FROM producto
                        WHERE valido=1
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