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
    public function addUsuario($cedula, $nombre, $clave_usuario, $id_cargo, $correo, $dpto) {
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
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuario (cedula, nombre, clave, id_cargo, correo, num_oficina) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssiss", $cedula, $nombre, $clave_usuario, $id_cargo, $correo, $dpto);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Usuario agregado exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al agregar usuario: ' . $stmt->error
            ];
        }
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
        $sql = "SELECT id_usuario, cedula, u.nombre, rs.nombre as nombre_cargo, u.num_oficina, o.nombre as nombre_oficina
                FROM rol_usuario rs INNER JOIN usuario u ON u.id_cargo=rs.id_cargo 
                INNER JOIN oficina o ON u.num_oficina=o.num_oficina";
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