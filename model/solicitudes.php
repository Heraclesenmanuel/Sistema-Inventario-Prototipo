<?php
require_once 'model/base.php';
class Solicitud extends Base
{
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
    public function obtenerSolicitudes()
    {
        $sql_extra = '';
        if ($_SESSION['dpto'] === 3) {
            $sql_extra = 'WHERE estado ="Pendiente"';
        } else if ($_SESSION['dpto'] === 4) {
            $sql_extra = 'WHERE estado ="En Revisión"';
        } else if ($_SESSION['dpto'] == 2) {
            $sql_extra = 'WHERE s.id_solicitante = ' . $_SESSION["id"] .
                ' OR s.num_oficina IN (
                        SELECT num_oficina FROM ofic_usuario 
                        WHERE id_usuario = ' . $_SESSION["id"] . '
                    )';
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

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        $datos = [];
        while ($row = $resultado->fetch_assoc()) {
            $datos[] = $row;
        }
        return $datos;
    }
    public function cargarProds()
    {
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
    public function getProdsPorIdSolic($id_solicitud)
    {
        $sql = "SELECT ps.*, p.nombre, p.medida, t.nombre as nombre_tipo FROM prod_solic ps 
                INNER JOIN solicitud s ON s.id_solicitud = ps.id_solicitud
                INNER JOIN producto p ON ps.id_producto=p.id_producto
                INNER JOIN tipo_prod t ON p.id_tipo = t.id_tipo
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
    public function validarTiposDatos($datos, $stmt, $id = null)
    {
        $id_solicitante = $datos['id_solicitante'];
        $ofic_solic = $datos['oficina_solic'];
        $fecha_deseada = $datos['fecha_deseada'];
        $comentarios = $datos['comentarios'];
        if ($id) {
            $stmt->bind_param('iisss', $id, $id_solicitante, $fecha_deseada, $comentarios, $ofic_solic);
        } else {
            $stmt->bind_param('isss', $id_solicitante, $fecha_deseada, $comentarios, $ofic_solic);
        }
        return $stmt;
    }
    public function guardarSolicitud($datos, $id = null)
    {
        $this->db->begin_transaction();
        if ($id) {
            $stmt = $this->db->prepare("INSERT INTO solicitud (id_solicitud, id_solicitante, fecha_solic, fecha_deseo, comentarios, num_oficina) VALUES (?, ?, NOW(), ?, ?, ?)");
        } else {
            $stmt = $this->db->prepare("INSERT INTO solicitud (id_solicitante, fecha_solic, fecha_deseo, comentarios, num_oficina) VALUES (?, NOW(), ?, ?, ?)");
        }
        if (!$stmt) {
            error_log("Error preparando al consulta de la solicitud");
            $this->db->rollback();
            $this->db->close();
            die('Error en la preparación de la consulta SQL');
        }
        $result = $this->validarTiposDatos($datos, $stmt, $id)->execute();
        if (!$result) {
            $this->db->rollback();
            error_log("Error al ejecutar la consulta: " . $stmt->error);
        }
        $stmt->close();

        return $result;
    }
    private function guardarProdInvalido($prod)
    {
        // Asegúrate de que estás recibiendo los datos correctos
        error_log("Datos recibidos en guardarProdInvalido: " . print_r($prod, true));

        $stmt = $this->db->prepare("INSERT INTO producto (nombre, medida, id_tipo, valido) VALUES (?, ?, ?, 0)");

        if (!$stmt) {
            error_log("Error preparando la consulta del producto invalido: " . $this->db->error);
            return false;
        }

        // VERIFICA: ¿'id_tipo' o 'tipo_producto'?
        $id_tipo = $prod['tipo_producto'] ?? $prod['id_tipo'] ?? null;

        if (!$id_tipo) {
            error_log("ERROR: No se recibió id_tipo en guardarProdInvalido");
            return false;
        }

        // CORRECCIÓN: El tercer parámetro es id_tipo, no cantidad
        $stmt->bind_param(
            'ssi',
            $prod['nombre_producto'],
            $prod['unidad_medida'],
            $id_tipo  // ← ¡ESTO ES IMPORTANTE!
        );

        $result = $stmt->execute();

        if (!$result) {
            error_log("Error al ejecutar la consulta de producto inválido: " . $stmt->error);
            $stmt->close();
            return false;
        }

        // OBTENER EL ID INMEDIATAMENTE
        $nuevo_id = $this->db->insert_id;

        error_log("Producto nuevo insertado - ID: $nuevo_id, Nombre: {$prod['nombre_producto']}");

        $stmt->close();

        return $nuevo_id;  // Devuelve el ID, no true/false
    }

    public function guardarProdsSolic($prods, $id_prods = null)
    {
        // Verificar que estamos en una transacción
        $id_solic = $this->db->insert_id;

        if (empty($prods)) {
            error_log("No se han recibido los productos correctamente");
            $this->db->rollback();
            return false;
        }

        error_log("Guardando " . count($prods) . " productos para solicitud ID: $id_solic");

        $stmt = $this->db->prepare("INSERT INTO prod_solic (id_solicitud, num_linea, un_deseadas, id_producto) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Error al preparar la consulta de productos: " . $this->db->error);
            $this->db->rollback();
            return false;
        }

        for ($i = 0; $i < count($prods); $i++) {
            error_log("--- Procesando producto $i ---");
            error_log("Datos producto: " . print_r($prods[$i], true));

            $id_prod = $prods[$i]['id_producto'] ?? '';

            // Si es producto nuevo (sin ID)
            if (empty($id_prod)) {
                error_log("Producto $i es NUEVO - Insertando en tabla producto...");

                $nuevo_id_prod = $this->guardarProdInvalido($prods[$i]);

                if ($nuevo_id_prod === false) {
                    error_log("ERROR: No se pudo guardar producto nuevo en iteración $i");
                    $this->db->rollback();
                    $stmt->close();
                    return false;
                }

                $id_prod = $nuevo_id_prod;
                error_log("Producto nuevo creado con ID: $id_prod");

            } else {
                error_log("Producto $i es EXISTENTE - ID: $id_prod");
            }

            // Insertar en prod_solic
            $num_linea = $i + 1;
            $cantidad = $prods[$i]['cantidad'];

            error_log("Insertando en prod_solic - Línea: $num_linea, Cantidad: $cantidad, ID Producto: $id_prod");

            $stmt->bind_param('iiii', $id_solic, $num_linea, $cantidad, $id_prod);
            $result = $stmt->execute();

            if (!$result) {
                error_log("ERROR al insertar en prod_solic: " . $stmt->error);
                $this->db->rollback();
                $stmt->close();
                return false;
            }

            error_log("✓ Producto $i guardado correctamente");
        }

        $stmt->close();

        // Todo salió bien, hacer commit
        $this->db->commit();
        error_log("Todos los productos guardados exitosamente para solicitud $id_solic");

        return true;
    }
    public function obtenerProdPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM producto WHERE id_producto = ?");

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function actualizarSolic($datos)
    {
        $stmt = $this->db->prepare("UPDATE prod_solic SET 
            nombre = ?, 
            medida = ?, 
            un_disponibles = ?, 
            id_tipo = ?,
            WHERE id_solicitud = ?");

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param(
            "sssis",
            $datos['codigo'],
            $datos['nombre'],
            $datos['medida'],
            $datos['un_disponibles'],
            $datos['tipo_producto'], //revisar si estasiendo capturado
            $datos['id_producto']
        );

        return $stmt->execute();
    }
    public function obtenerEstadisticas()
    {
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