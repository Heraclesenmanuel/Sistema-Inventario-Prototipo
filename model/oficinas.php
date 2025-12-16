<?php
require_once 'model/base.php';
//CAMBIAR NOMBRES DE FUNCIONES Y VARIABLES A OFICINA
class Oficina extends Base{
    public function getOficinas(){
        try {
            $sql = 'SELECT of.num_oficina as num_oficina, of.nombre as nombre, of.telefono as telefono, dir.nombre as nombre_dir, dir.ced_dir as ced_dir, dir.telf as telf_dir
            FROM oficina of LEFT JOIN director dir ON of.ced_dir=dir.ced_dir';
            $resul = $this->db->query($sql);

            if (!$resul) {
                error_log("Error en consulta SQL: " . $this->db->error);
                return [];
            }

            $datos = [];
            while ($rows = $resul->fetch_assoc()) {
                $datos[] = $rows;
            }
            return $datos;
        } catch (Exception $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        }
    }
    public function verificarOficina($num_oficina): bool
    {
        try {
        // Verificar si la cédula ya existe
            $sqlCheck = "SELECT num_oficina FROM oficina WHERE num_oficina = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            
            if ($stmtCheck) {
                $stmtCheck->bind_param("s", $num_oficina);
                $stmtCheck->execute();
                $result = $stmtCheck->get_result();
                if ($result->num_rows > 0) {
                    error_log("La oficina ya existe en la base de datos");
                    return false;
                }
                return true;  
            }
            else
            {
                error_log("Error en prepare: " . $stmtCheck->error);
                return false;
            }
            } catch (Exception $e) {
            error_log("Error al agregar usuario: " . $e->getMessage());
            return false;
            }
    }
    public function verificarDir($cedula): bool
    {
        try {
        // Verificar si la cédula ya existe
            $sqlCheck = "SELECT ced_dir FROM director WHERE ced_dir = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            
            if ($stmtCheck) {
                $stmtCheck->bind_param("s", $cedula);
                $stmtCheck->execute();
                $result = $stmtCheck->get_result();
                if ($result->num_rows > 0) {
                    error_log("La cédula ya existe en la base de datos");
                    return false;
                }
                return true;  
            }
            else
            {
                error_log("Error en prepare: " . $stmtCheck->error);
                return false;
            }
            } catch (Exception $e) {
            error_log("Error al agregar usuario: " . $e->getMessage());
            return false;
            }
    }
    public function agregarOficina($num_oficina, $nombre, $cedula, $telefono){
        try {
            if($this->verificarOficina($num_oficina))
            {
                $sql = "INSERT INTO oficina (num_oficina, nombre, ced_dir, telefono) VALUES (?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssss", $num_oficina, $nombre, $cedula, $telefono);
                    $resultado = $stmt->execute();

                    if (!$resultado) {
                        error_log("Error al ejecutar insert: " . $stmt->error);
                    }
                    return $resultado;
                } else {
                    error_log("Error al preparar statement: " . $this->db->error);
                    return false;
                }
            }
        }
        catch (Exception $e) {
            error_log("Error al agregar usuario: " . $e->getMessage());
            return false;
        }
    }
    public function deleteOficina($id){
        try {
            $sql = "DELETE FROM oficina WHERE num_oficina = ?";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $id);
                $resultado = $stmt->execute();
                
                if (!$resultado) {
                    error_log("Error al eliminar Oficina: " . $stmt->error);
                    return false;
                }
                
                // Verificar si realmente se eliminó algo
                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    error_log("No se encontró Oficina con ID: " . $id);
                    return false;
                }
            } else {
                error_log("Error al preparar statement delete: " . $this->db->error);
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al eliminar Oficina: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerOficinaPorId($id){
        try {
            $sql = "SELECT * FROM oficina WHERE num_oficina = ?";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    return $result->fetch_assoc();
                }
            }
            return null;
        } catch (Exception $e) {
            error_log("Error al obtener Oficina por ID: " . $e->getMessage());
            return null;
        }
    }

    public function actualizarOficina($id, $nombre, $cedula, $telefono){
        try {
            $sql = "UPDATE oficina SET nombre = ?, cedula = ?, telefono = ? WHERE num_oficina = ?";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssi", $nombre, $cedula, $telefono, $id);
                return $stmt->execute();
            }
            return false;
        } catch (Exception $e) {
            error_log("Error al actualizar Oficina: " . $e->getMessage());
            return false;
        }
    }
    public function agregarDir($nombre, $cedula, $telefono){
        try {
            if($this->verificarDir($cedula))
            {
                $sql = "INSERT INTO director VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                
                if ($stmt) {
                    $stmt->bind_param("sss", $cedula, $nombre, $telefono);
                    $resultado = $stmt->execute();
                    
                    if (!$resultado) {
                        error_log("Error al ejecutar insert: " . $stmt->error);
                    }
                    return $resultado;
                } else {
                    error_log("Error al preparar statement: " . $this->db->error);
                    return false;
                }
            }
            return true;
        }
        catch (Exception $e) {
            error_log("Error al agregar usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>