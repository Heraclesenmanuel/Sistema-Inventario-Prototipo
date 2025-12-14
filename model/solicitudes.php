<?php
require_once 'model/base.php';
class Solicitud extends Base{
    public function contarSolictsNoEnRev($solicitudes)
    {
        return count(array_filter($solicitudes, fn($solicitud) => $solicitud['estado'] !== "En revision"));
    }
    public function actualizarEstadoSolicitud($idSolicitud, $nuevoEstado, $motivo)
    {
        $sql = "UPDATE solicitud SET
                estado = ?,
                comentarios = ?
                WHERE id_solicitud = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssi', $nuevoEstado, $motivo, $idSolicitud);
        $stmt->execute();
        return $stmt->affected_rows;
    }
    public function obtenerSolicitudes(){
        if($_SESSION['dpto'] === 3)
        {
            $sql_extra = 'WHERE estado ="Pendiente"';
        }
        else if($_SESSION['dpto'] === 4)
        {
            $sql_extra = 'WHERE estado ="En Revisión"';
        }
        else
        {
            $sql_extra = 'WHERE of_u.id_usuario = '. $_SESSION["id"] /*.' AND estado = '*/;
        }
            $sql = 'SELECT s.*, 
                u.nombre AS nombre_solicitante, 
                f.nombre AS nombre_oficina
            FROM solicitud s
            INNER JOIN usuario u 
                ON s.id_solicitante = u.id_usuario
            INNER JOIN oficina f 
                ON s.num_oficina = f.num_oficina '
            . $sql_extra . '
            ORDER BY s.fecha_solic DESC;
                ';
        if($_SESSION['dpto'] < 3)
        {
            $sql = 'SELECT 
                        s.*,
                        u.nombre AS nombre_solicitante,
                        f.nombre AS nombre_oficina,
                        s.estado
                    FROM solicitud s
                    INNER JOIN oficina f 
                        ON s.num_oficina = f.num_oficina
                    INNER JOIN usuario u 
                        ON s.id_solicitante = u.id_usuario
                    INNER JOIN ofic_usuario of_u 
                        ON s.num_oficina = of_u.num_oficina ' .
                        $sql_extra .
                    ' ORDER BY s.fecha_solic DESC;';
        }
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
    public function cargarProds() {
        $sql = "SELECT * FROM producto";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $oficinas = [];

        while ($row = $result->fetch_assoc()) {
            $oficinas[] = $row;
        }

        return [
            'data' => $oficinas,
            'success' => true
        ];
    }
    public function getProdsPorIdSolic($id_solicitud) {
        $sql = "SELECT ps.*, t.nombre as nombre_tipo FROM prod_solic ps 
                INNER JOIN solicitud s ON s.id_solicitud = ps.id_solicitud
                INNER JOIN tipo_prod t ON ps.id_tipo = t.id_tipo
                WHERE ps.id_solicitud = ?
                ORDER BY ps.num_linea";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id_solicitud);
        $stmt->execute();
        $result = $stmt->get_result();
        $productos_solic = [];

        while ($row = $result->fetch_assoc()) {
            $productos_solic[] = $row;
        }

        return [
            'data' => $productos_solic,
            'success' => true
        ];
    }
    public function eliminarSolicitud($id)
    {
        $query = "DELETE FROM solicitud WHERE id_solicitud = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $resultado = $stmt->execute();
        
        return $resultado;
    }
    public function validarTiposDatos($datos, $stmt, $id=null)
    {
        $id_solicitante = $_SESSION['id'];
        $ofic_solic = $datos['oficina_solic'];
        $fecha_deseada = $datos['fecha_deseada'];
        $comentarios = $datos['comentarios'];
        if($id)
        {
            $stmt->bind_param('iisss', $id, $id_solicitante, $fecha_deseada, $comentarios, $ofic_solic); 
        }
        else
        {
            $stmt->bind_param('isss', $id_solicitante, $fecha_deseada, $comentarios, $ofic_solic);
        }
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
    public function guardarSolicitud($datos, $id = null) {
        if($id)
        {
            $stmt = $this->db->prepare("INSERT INTO solicitud (id_solicitud, id_solicitante, fecha_solic, fecha_deseo, comentarios, num_oficina) VALUES (?, ?, NOW(), ?, ?, ?)");
        }
        else
        {
            $stmt = $this->db->prepare("INSERT INTO solicitud (id_solicitante, fecha_solic, fecha_deseo, comentarios, num_oficina) VALUES (?, NOW(), ?, ?, ?)");
        }
        if (!$stmt) {
            $this->db->close();
            die('Error en la preparación de la consulta SQL');
        }
        $result = $this->validarTiposDatos($datos, $stmt, $id)->execute();
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
            $result = $this->validarDatosProds($prods[$i], $stmt, $i+1, $id_solic)->execute();
            if (!$result) 
            {
                error_log("Error al ejecutar la consulta: " . $stmt->error);
            }
        }

        $stmt->close();
        return $result;
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
        $stmt = $this->db->prepare("UPDATE prod_solic SET 
            nombre = ?, 
            medida = ?, 
            un_disponibles = ?, 
            id_tipo = ?,
            WHERE id_solicitud = ?");
        
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