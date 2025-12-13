<?php
require_once 'model/base.php';
class Notificaciones extends Base{
    public function obtenerDatos($id_usuario){
        $sql = 'SELECT n.*, tn.mensaje as mensaje, rn.leido as leido, rn.subtitulo as subtitulo
                FROM receptor_notif rn
                INNER JOIN notificacion n ON rn.id_notif=n.id_notif
                INNER JOIN tipo_notif tn ON tn.id_tipo_notif=n.tipo
                WHERE rn.id_usuario = ?
                ORDER BY rn.leido ASC, fecha_notif DESC';
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
        try
        {
            $stmt->bind_param('i', $id_usuario);
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
    public function validarTiposDatos($datos, $stmt)
    {
        /*
        $codigo = $datos['codigo'];
        $nombre = $datos['nombre'];
        $un_disponibles = (int)$datos['un_disponibles'];
        $tipo_p = $datos['tipo_p'];
        $medida = $datos['medida'];

        $stmt->bind_param('ssisi', $codigo, $nombre, $un_disponibles, $medida, $tipo_p);
        
        return $stmt;*/
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
        public function eliminarNotif($id, $id_usuario) {
            $stmt = $this->db->prepare("DELETE FROM receptor_notif WHERE id_notif = ? AND id_usuario = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            try {
                $stmt->bind_param("ii", $id, $id_usuario);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    throw new Exception("No se encontró la notificacion con ID $id");
                }
            } finally {
                $stmt->close();
            }
        }
        public function leerNotif($id, $id_usuario) {
            $stmt = $this->db->prepare("UPDATE receptor_notif 
                                        SET leido=1 
                                        WHERE id_notif = ? AND id_usuario = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            try {
                $stmt->bind_param("ii", $id, $id_usuario);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    throw new Exception("No se encontró la notificacion con ID $id");
                }
            } finally {
                $stmt->close();
            }
        }
        public function leerTodaNotif($id) {
            $stmt = $this->db->prepare("UPDATE receptor_notif SET leido=1 
                                                WHERE id_usuario = ? AND leido=0");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            try {
                $stmt->bind_param("i", $id);
                $resultado = $stmt->execute();
                if ($resultado) {
                    return true;
                } else {
                    throw new Exception("No se encontró la notificacion con ID $id");
                }
            } finally {
                $stmt->close();
            }
        }
        public function limpiarNotifs($id) {
            $stmt = $this->db->prepare("DELETE FROM receptor_notif WHERE id_usuario = ? AND leido=1");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            try {
                $stmt->bind_param("i", $id);
                $resultado = $stmt->execute();
                if ($resultado) {
                    return true;
                } else {
                    throw new Exception("No se encontró la notificacion con ID $id");
                }
            } finally {
                $stmt->close();
            }
        }
    public function obtenerNotifPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM notificacion WHERE id_notif = ?");
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    private function chequearPorRutina($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT COUNT(rn.id_notif) AS total
                                            FROM receptor_notif rn
                                            INNER JOIN notificacion n ON rn.id_notif=n.id_notif
                                            WHERE n.fecha_notif < DATE_SUB(NOW(), INTERVAL 7 DAY)
                                            AND rn.leido = 1
                                            AND rn.id_usuario = ?");
            if (!$stmt) {
                error_log("Error al preparar la consulta: " . $this->db->error);
                return false;
            }
            try {
                $stmt->bind_param("i", $id_usuario);
                $stmt->execute();
                $result = $stmt->get_result();
                if ((int)$result->fetch_assoc()['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            } finally {
                $stmt->close();
            }
    }
    public function eliminarPorRutina($id_usuario)
    {
        if($this->chequearPorRutina($id_usuario))
        {
            $stmt = $this->db->prepare("DELETE rn 
                                            FROM receptor_notif rn 
                                            INNER JOIN notificacion n ON rn.id_notif=n.id_notif
                                            WHERE n.fecha_notif < DATE_SUB(NOW(), INTERVAL 7 DAY)
                                            AND rn.leido = 1
                                            AND rn.id_usuario = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            try {
                $stmt->bind_param("i", $id_usuario);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    throw new Exception("No se encontró la notificacion con ID $id_usuario");
                }
            } finally {
                $stmt->close();
            }
        }
    }
}
?>