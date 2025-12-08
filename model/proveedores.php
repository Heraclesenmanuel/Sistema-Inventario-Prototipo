<?php
require_once 'model/base.php';
class Proveedores extends Base{
    public function obtenerProveedores() {
        $sql = 'SELECT * FROM proveedor';
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return [];
        }
        
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return [];
        }
        
        $resultado = $stmt->get_result();
        $datos = [];
        
        while ($row = $resultado->fetch_assoc()) {
            $datos[] = $row;
        }
        
        $stmt->close();
        return $datos;
    }
    public function chequearExistenciaRif($nuevoRif) 
    {
        $sql = "SELECT COUNT(*) FROM proveedor 
                WHERE rif = ?;";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $nuevoRif);
        $stmt->execute();

        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close(); // muy importante

        return ($count == 0);
    }
    public function chequearConvergenciaRif($nuevoRif, $rifOriginal) 
    {
        $sql = "SELECT COUNT(*) FROM proveedor 
                WHERE rif = ? AND rif != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nuevoRif, $rifOriginal);
        $stmt->execute();

        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return ($count == 0);
    }
    private function agregarProvRecomendacion($recomendaciones, $rif)
    {
        $query = "INSERT INTO prov_recomendaciones (rif_proveedor, id_tipo) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        foreach($recomendaciones as $categoria)
        {
            $id_tipo = intval($categoria);
            $stmt->bind_param('si', $rif, $id_tipo);
            $resultado = $stmt->execute();
            if (!$resultado) {
                error_log("Error ejecutando inserción: " . $stmt->error);
                return false;
            }
        }
        return true;
    }
    private function eliminarProvRecomendaciones($rif)
    {
        $sql = 'DELETE FROM prov_recomendaciones WHERE rif_proveedor=?';
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $rif);
        
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return false;
        }
        
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return false;
        }
        $stmt->close();
        return true;
    }
    public function agregarProveedor($data) {
        $this->db->begin_transaction();

        try 
        {
            $query = "INSERT INTO proveedor (nombre, email, telefono, direccion, estado, nota, rif) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                $this->db->rollback();
                return false;
            }
            $stmt->bind_param("sssssss", 
                $data['nombre_proveedor'],
                $data['email'],
                $data['telefono'],
                $data['direccion'],
                $data['estado'],
                $data['nota'],
                $data['rif']
            );
            $resultado = $stmt->execute();
            if (!$resultado || !$this->agregarProvRecomendacion($data['categorias_recomendadas'], $data['rif'])) {
                $this->db->rollback();
                error_log("Error ejecutando inserción: " . $stmt->error);
            }
            else { $this->db->commit(); }
        }
        catch(Exception $e)
        {
            $this->db->rollback();
            error_log("Error en la transacción pepe: " . $e->getMessage());
        }
        $stmt->close();
        return $resultado;
    }

    public function actualizarProveedor($data) {
        $this->db->begin_transaction();
        $query = "UPDATE proveedor SET nombre = ?, email = ?, telefono = ?, direccion = ?, rif = ?, estado = ?, nota = ? WHERE rif = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("ssssssss", 
            $data['nombre_proveedor'],
            $data['email'],
            $data['telefono'],
            $data['direccion'],
            $data['rif'],
            $data['estado'],
            $data['nota'],
            $data['rif_original']
        );
        $resultado = $stmt->execute();
        
        if (!$resultado || !$this->eliminarProvRecomendaciones($data['rif_original']) || !$this->agregarProvRecomendacion($data['categorias_recomendadas'], $data['rif'])) {
            $this->db->rollback();
            error_log("Error ejecutando actualización: " . $stmt->error);
        }
        else
        {
            $this->db->commit();
        }
        $stmt->close();
        
        return $resultado;
    }

    public function eliminarProveedor($rif) {
        $query = "DELETE FROM proveedor WHERE rif = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("s", $rif);
        $resultado = $stmt->execute();
        $stmt->close();
        
        return $resultado;
    }

    public function obtenerProveedorPorId($rif) {
        $query = "SELECT * FROM proveedor WHERE rif = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return null;
        }
        
        $stmt->bind_param("s", $rif);
        
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return null;
        }
        
        $resultado = $stmt->get_result();
        $proveedor = $resultado->fetch_assoc();
        $stmt->close();
        
        return $proveedor;
    }
}
?>