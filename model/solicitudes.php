<?php
require_once 'model/base.php';
class Solicitud extends Base{
    public function contarSolictsNoEnRev($solicitudes)
    {
        return count(array_filter($solicitudes, fn($solicitud) => $solicitud['estado'] !== "En revision"));
    }
    public function obtenerSolicitudes(){
        $sql = 'SELECT s.*, u.nombre as nombre_solicitante, f.nombre as nombre_oficina
        FROM solicitud s INNER JOIN usuario u ON s.id_solicitante=u.id_usuario JOIN oficina f ON u.num_oficina=f.num_oficina';
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
    public function validarTiposDatos($datos, $stmt)
    {
        $id_solicitante = $_SESSION['id'];
        $ofic_solic = $datos['oficina_solic'];
        $fecha_deseada = $datos['fecha_deseada'];
        $comentarios = $datos['comentarios'];

        $stmt->bind_param('isss', $id_solicitante, $fecha_deseada, $comentarios, $ofic_solic);
        
        return $stmt;
    }
    public function validarDatosProds($datos, $stmt, $num_linea, $id_solic)
    {
        $nombre_prod = $datos['nombre_producto'];
        $unidad_medida = $datos['unidad_medida'];
        $cantidad = $datos['cantidad'];
        $tipo_producto = $datos['tipo_producto'];
        $stmt->bind_param('iisisi', $id_solic, $num_linea, $nombre_prod, $cantidad, $unidad_medida, $tipo_producto);
        
        return $stmt;
    }
    public function guardarSolicitud($datos) {
        $stmt = $this->db->prepare("INSERT INTO solicitud (id_solicitante, fecha_solic, fecha_deseo, comentarios, num_oficina) VALUES (?, NOW(), ?, ?, ?)");
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
    public function guardarProds($prods)
    {
        $id_solic = $this->db->insert_id;
        if (empty($prods))
        {
            return false;
        }
        for ($i=0; $i<count($prods); $i++)
        {
            $stmt = $this->db->prepare("INSERT INTO prod_solic (id_solicitud, num_linea, nombre, un_deseadas, medida, id_tipo) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt)
            {
                $this->db->close();
                die('Error en la preparación de la consulta SQL');
            }
            $result = $this->validarDatosProds($prods[$i], $stmt, $i, $id_solic)->execute();
            if (!$result) 
            {
                error_log("Error al ejecutar la consulta: " . $stmt->error);
            }
        }

        $stmt->close();
        return $result;
    }
        public function eliminarSolicitud($id) {
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
    public function obtenerSolicPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM producto WHERE id_producto = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function actualizarSolic($datos) {
        $stmt = $this->db->prepare("UPDATE producto SET 
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
            $datos['tipo_producto'], //revisar si estasiendo capturado
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
                        FROM producto
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