<?php
require_once 'model/base.php';
class Config extends Base{

    /*public function updateDollar($nuevoValor) {
        // Validación básica
        if (!is_numeric($nuevoValor) || $nuevoValor <= 0) {
            return [
                'success' => false,
                'message' => 'El valor debe ser un número mayor a cero'
            ];
        }

        $sql = "UPDATE admin SET precio_dollar = ? WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("d", $nuevoValor);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Precio del dólar actualizado',
                'new_value' => $nuevoValor
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar: ' . $stmt->error
            ];
        }
    }*/

    public function updateNombre($nuevoValor) {
        // Validación básica
        if (empty($nuevoValor) || strlen($nuevoValor) > 100) {
            return [
                'success' => false,
                'message' => 'El nombre no puede estar vacío y debe tener menos de 100 caracteres'
            ];
        }

        $sql = "UPDATE config_pag SET nombreAPP = ? WHERE id_config = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nuevoValor);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Nombre de la empresa actualizado',
                'new_value' => $nuevoValor
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar: ' . $stmt->error
            ];
        }
    }

    public function updateClave($nuevoValor) {
        // Validación básica
        if (empty($nuevoValor) || strlen($nuevoValor) > 100) {
            return [
                'success' => false,
                'message' => 'La clave no puede estar vacía y debe tener menos de 100 caracteres'
            ];
        }

        // Validación mínima de longitud para seguridad
        if (strlen($nuevoValor) < 6) {
            return [
                'success' => false,
                'message' => 'La clave debe tener al menos 6 caracteres'
            ];
        }

        $sql = 'UPDATE usuario_super SET claveSuper = ? WHERE id_usuario = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevoValor, $_SESSION['id']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Clave actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar: ' . $stmt->error
            ];
        }
    }

    public function verificarUsuario($cedula)
    {
        // Verificar si la cédula ya existe
        $sqlCheck = "SELECT id_usuario FROM usuario WHERE cedula = ?";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $cedula);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            return [
                'success' => false,
                'message' => 'La cédula ya está registrada.'
            ];
        }
        $stmtCheck->close();
    }
    public function addUsuario($cedula, $nombre, $clave_usuario, $id_cargo, $correo, $oficinas) {
        // Validaciones básicas
        if (!preg_match('/^[0-9]{7,10}$/', $cedula)) {
            return [
                'success' => false,
                'message' => 'Cédula inválida. Debe contener entre 7 y 10 dígitos numéricos.'
            ];
        }
        $verificacion = $this->verificarUsuario($cedula);
        if (is_array($verificacion))
        {
            return $verificacion;
        }
        $this->db->begin_transaction();
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuario (cedula, nombre, clave, id_cargo, correo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssis", $cedula, $nombre, $clave_usuario, $id_cargo, $correo);

        if (!$stmt->execute() || !$this->addOficinasUsuario($this->db->insert_id, $oficinas)) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Error al agregar usuario: ' . $stmt->error
            ];
        } else {
            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Usuario agregado exitosamente.'
            ];
        }
    }
    private function addOficinasUsuario($id_usuario, $oficinas)
    {   
        if(empty($oficinas))
        {
            return false;
        }
        $query = "INSERT INTO ofic_usuario VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        foreach($oficinas as $oficina)
        {
            $num_ofic = $oficina;
            $stmt->bind_param('si', $num_ofic, $id_usuario);
            $resultado = $stmt->execute();
            if (!$resultado) {
                error_log("Error ejecutando inserción: " . $stmt->error);
                return false;
            }
        }
        return true;
    }
    public function getOficinasUsuario($id_usuario)
    {
        $sql = "SELECT of.nombre as nombre
        FROM oficina of INNER JOIN ofic_usuario of_u ON of.num_oficina=of_u.num_oficina
        WHERE of_u.id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if(!$result)
        {
            error_log("Error accediendo a los datos de Recomendacion para este RIF.");
            return false;
        }
        $rows = $result->fetch_all(MYSQLI_NUM);
        $valores = array_column($rows, 0);
        return $valores;
    }
    public function deleteUsuario($id_usuario) {
        // Validación básica
        if (!is_numeric($id_usuario) || $id_usuario <= 0) {
            return [
                'success' => false,
                'message' => 'ID de usuario inválido.'
            ];
        }

        $sql = "DELETE FROM usuario WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se encontró el usuario con el ID proporcionado.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $stmt->error
            ];
        }
    }

    public function mostrarUsuarios() {
        $sql = "SELECT u.id_usuario, cedula, u.nombre, rs.nombre as nombre_cargo, o.nombre as nombre_oficina, COUNT(of_u.num_oficina) AS cantidad_oficinas_afiliadas
                FROM rol_usuario rs INNER JOIN usuario u ON u.id_cargo=rs.id_cargo 
                INNER JOIN ofic_usuario of_u ON u.id_usuario=of_u.id_usuario
                INNER JOIN oficina o ON of_u.num_oficina=o.num_oficina
                GROUP BY u.id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuarios = [];

        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        return [
            'success' => true,
            'data' => $usuarios
        ];
    }
    public function verif()
    {
        $sql = "SELECT claveSuper FROM usuario_super WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();;


        return $result->fetch_assoc();
    }
}

?>