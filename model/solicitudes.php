<?php
require_once 'model/base.php';
class Solicitud extends Base
{
    public function contarSolictsNoEnRev($solicitudes)
    {
        return count(array_filter($solicitudes, fn($solicitud) => $solicitud['estado'] !== "En revision"));
    }
    public function generarNotificacionRechazada($idUsuario, $idSolicitud, $motivo = '')
    {
        try {
            // Insertar la notificación
            $query = "INSERT INTO notificacion (tipo, fecha_notif) VALUES (1, NOW())";
            $result = $this->db->query($query);

            if (!$result) {
                throw new Exception("Error al insertar notificación: " . $this->db->error);
            }

            $idNotif = $this->db->insert_id;

            // Insertar en receptor_notif con subtítulo personalizado
            $subtitulo = "Solicitud #{$idSolicitud} rechazada";
            if (!empty($motivo)) {
                $subtitulo .= " - Motivo: {$motivo}";
            }

            $queryReceptor = "INSERT INTO receptor_notif (id_usuario, id_notif, leido, subtitulo) 
                             VALUES (?, ?, 0, ?)";
            $stmtReceptor = $this->db->prepare($queryReceptor);
            $stmtReceptor->bind_param("iis", $idUsuario, $idNotif, $subtitulo);

            if (!$stmtReceptor->execute()) {
                throw new Exception("Error al insertar receptor: " . $stmtReceptor->error);
            }

            $stmtReceptor->close();
            return true;

        } catch (Exception $e) {
            error_log("Error al generar notificación de rechazo: " . $e->getMessage());
            return false;
        }
    }
    public function generarNotificacionRevisionPendiente($idUsuario, $idSolicitud)
    {
        try {
            // Obtener usuarios con rol de Presupuesto (id_cargo = 4)
            $queryUsuarios = "SELECT id_usuario FROM usuario WHERE id_cargo = 4";
            $resultUsuarios = $this->db->query($queryUsuarios);

            if (!$resultUsuarios) {
                throw new Exception("Error al obtener usuarios: " . $this->db->error);
            }

            $usuariosPresupuesto = [];
            while ($row = $resultUsuarios->fetch_assoc()) {
                $usuariosPresupuesto[] = $row['id_usuario'];
            }

            if (empty($usuariosPresupuesto)) {
                return false;
            }

            // Insertar una notificación
            $queryNotif = "INSERT INTO notificacion (tipo, fecha_notif) VALUES (2, NOW())";
            $resultNotif = $this->db->query($queryNotif);

            if (!$resultNotif) {
                throw new Exception("Error al insertar notificación: " . $this->db->error);
            }

            $idNotif = $this->db->insert_id;
            $subtitulo = "Solicitud #{$idSolicitud} necesita revisión";

            // Para cada usuario de presupuesto, crear registro en receptor_notif
            $queryReceptor = "INSERT INTO receptor_notif (id_usuario, id_notif, leido, subtitulo) 
                             VALUES (?, ?, 0, ?)";
            $stmtReceptor = $this->db->prepare($queryReceptor);

            foreach ($usuariosPresupuesto as $usuarioId) {
                $stmtReceptor->bind_param("iis", $usuarioId, $idNotif, $subtitulo);
                if (!$stmtReceptor->execute()) {
                    error_log("Error al insertar receptor para usuario {$usuarioId}: " . $stmtReceptor->error);
                }
            }

            $stmtReceptor->close();
            return true;

        } catch (Exception $e) {
            error_log("Error al generar notificación de revisión pendiente: " . $e->getMessage());
            return false;
        }
    }

    public function generarNotificacionAceptada($idUsuario, $idSolicitud)
    {
        try {
            // Insertar la notificación
            $query = "INSERT INTO notificacion (tipo, fecha_notif) VALUES (3, NOW())";
            $result = $this->db->query($query);

            if (!$result) {
                throw new Exception("Error al insertar notificación: " . $this->db->error);
            }

            $idNotif = $this->db->insert_id;

            // Insertar en receptor_notif
            $subtitulo = "Solicitud #{$idSolicitud} aprobada exitosamente";

            $queryReceptor = "INSERT INTO receptor_notif (id_usuario, id_notif, leido, subtitulo) 
                             VALUES (?, ?, 0, ?)";
            $stmtReceptor = $this->db->prepare($queryReceptor);
            $stmtReceptor->bind_param("iis", $idUsuario, $idNotif, $subtitulo);

            if (!$stmtReceptor->execute()) {
                throw new Exception("Error al insertar receptor: " . $stmtReceptor->error);
            }

            $stmtReceptor->close();
            return true;

        } catch (Exception $e) {
            error_log("Error al generar notificación de aceptación: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerIdSolicitante($idSolicitud)
    {
        try {
            $query = "SELECT id_solicitante FROM solicitud WHERE id_solicitud = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $idSolicitud);
            $stmt->execute();

            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            $stmt->close();

            return $row ? $row['id_solicitante'] : null;

        } catch (Exception $e) {
            error_log("Error al obtener solicitante: " . $e->getMessage());
            return null;
        }
    }
    public function getLastInsertId()
    {
        return $this->db->insert_id;
    }
    public function generarNotificacionPersonalizada($idUsuario, $tipo, $subtitulo = '')
    {
        try {
            // Insertar la notificación
            $query = "INSERT INTO notificacion (tipo, fecha_notif) VALUES (?, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $tipo);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar notificación: " . $stmt->error);
            }
            
            $idNotif = $this->db->insert_id;
            $stmt->close();
            
            // Si se proporciona un usuario específico
            if ($idUsuario) {
                $queryReceptor = "INSERT INTO receptor_notif (id_usuario, id_notif, leido, subtitulo) 
                                 VALUES (?, ?, 0, ?)";
                $stmtReceptor = $this->db->prepare($queryReceptor);
                $stmtReceptor->bind_param("iis", $idUsuario, $idNotif, $subtitulo);
                
                if (!$stmtReceptor->execute()) {
                    throw new Exception("Error al insertar receptor: " . $stmtReceptor->error);
                }
                
                $stmtReceptor->close();
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error al generar notificación personalizada: " . $e->getMessage());
            return false;
        }
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
    public function validarProducto($idProducto)
    {
        $sql = "UPDATE producto SET
                valido = 1
                WHERE id_producto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $idProducto);
        $result = $stmt->execute();
        return $result;
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
        $sql = "SELECT p.*, ps.un_deseadas
                FROM producto p
                LEFT JOIN prod_solic ps ON p.id_producto=ps.id_producto";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $productos = [];

        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        return [
            'data' => $productos,
            'success' => true
        ];
    }
 public function getUsuariosPorRol($idRol)
    {
        $query = "SELECT id_usuario FROM usuario WHERE id_cargo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idRol);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $usuarios = [];
        
        while ($row = $resultado->fetch_assoc()) {
            $usuarios[] = $row['id_usuario'];
        }
        
        $stmt->close();
        return $usuarios;
    }
    
    /**
     * Obtener detalles del solicitante
     */
    public function getSolicitanteInfo($idSolicitud)
    {
        $query = "SELECT u.id_usuario, u.nombre, u.correo 
                  FROM solicitud s 
                  INNER JOIN usuario u ON s.id_solicitante = u.id_usuario 
                  WHERE s.id_solicitud = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idSolicitud);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        
        return $row;
    }
    
    /**
     * Obtener notificaciones no leídas de un usuario
     */
    public function getNotificacionesNoLeidas($idUsuario)
    {
        $query = "SELECT n.id_notif, n.tipo, n.fecha_notif, tn.mensaje, rn.subtitulo, rn.leido
                  FROM receptor_notif rn
                  INNER JOIN notificacion n ON rn.id_notif = n.id_notif
                  INNER JOIN tipo_notif tn ON n.tipo = tn.id_tipo_notif
                  WHERE rn.id_usuario = ? AND rn.leido = 0
                  ORDER BY n.fecha_notif DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $notificaciones = [];
        
        while ($row = $resultado->fetch_assoc()) {
            $notificaciones[] = $row;
        }
        
        $stmt->close();
        return $notificaciones;
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarNotificacionLeida($idUsuario, $idNotif)
    {
        $query = "UPDATE receptor_notif SET leido = 1 
                  WHERE id_usuario = ? AND id_notif = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $idUsuario, $idNotif);
        
        $resultado = $stmt->execute();
        $stmt->close();
        
        return $resultado;
    }
    public function insertarRegistrosProductos($id_solicitud, $asignaciones)
    {
        foreach ($asignaciones as $asignacion) {
            $sql = "INSERT INTO registro_prod 
                (id_solicitud, num_linea, un_anadidas, rif_proveedor, fecha_r) 
                VALUES (?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(
                "iiis",
                $asignacion['id_solicitud'],
                $asignacion['num_linea'],
                $asignacion['cantidad_suplir'],
                $asignacion['rif_proveedor']
            );

            if (!$stmt->execute() || !$this->validarProducto($asignacion['id_producto'])) {
                throw new Exception("Error insertando registro_prod: " . $stmt->error);
            }

            $stmt->close();
        }
    }
    public function getProdsPorIdSolic($id_solicitud)
    {
        $sql = "SELECT ps.*, p.nombre, p.medida, p.id_tipo, t.nombre as nombre_tipo, ps.un_deseadas
                FROM prod_solic ps 
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

        error_log("ELIMINANDO");
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

        $stmt = $this->db->prepare("INSERT INTO producto (nombre, medida, id_tipo, fecha_r, valido) VALUES (?, ?, ?, NOW(), 0)");

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
                        p.nombre, 
                        SUM(rp.un_anadidas) as cantidad,
                        ROUND(SUM(rp.un_anadidas) * 100.0 / (SELECT SUM(rp.un_anadidas) FROM producto 
                        INNER JOIN prod_solic ps ON p.id_producto=ps.id_producto
                        INNER JOIN registro_prod rp ON rp.id_solicitud=ps.id_solicitud
                        AND rp.num_linea=ps.num_linea), 2) as porcentaje
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