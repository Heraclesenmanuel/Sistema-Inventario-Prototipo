<?php
require_once 'model/base.php';

class Reportes extends Base
{

    // Tipos de productos solicitados por oficina
    public function obtenerTiposProductosPorOficina()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                tp.nombre as tipo_producto,
                COUNT(DISTINCT ps.id_solicitud) as cantidad
            FROM oficina o
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            LEFT JOIN prod_solic ps ON s.id_solicitud = ps.id_solicitud
            INNER JOIN producto p ON ps.id_producto = p.id_producto
            LEFT JOIN tipo_prod tp ON p.id_tipo = tp.id_tipo
            WHERE tp.nombre IS NOT NULL
            GROUP BY o.nombre, tp.nombre
            ORDER BY o.nombre, cantidad DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Frecuencia de solicitudes por oficina
    public function obtenerFrecuenciaSolicitudesPorOficina()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                COUNT(s.id_solicitud) as total_solicitudes
            FROM oficina o
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            GROUP BY o.nombre
            HAVING total_solicitudes > 0
            ORDER BY total_solicitudes DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Cantidad de productos solicitados por oficina
    public function obtenerCantidadProductosPorOficina()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                COALESCE(SUM(ps.un_deseadas), 0) as total_productos
            FROM oficina o
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            LEFT JOIN prod_solic ps ON s.id_solicitud = ps.id_solicitud
            GROUP BY o.nombre
            HAVING total_productos > 0
            ORDER BY total_productos DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Cantidad de solicitudes rechazadas por oficina
    public function obtenerSolicitudesRechazadasPorOficina()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                COUNT(s.id_solicitud) as total_rechazadas
            FROM oficina o
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina AND s.estado = 'Rechazado'
            GROUP BY o.nombre
            HAVING total_rechazadas > 0
            ORDER BY total_rechazadas DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Cantidad de usuarios por oficina
    public function obtenerUsuariosPorOficina()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                COUNT(u.id_usuario) as total_usuarios
            FROM oficina o
            INNER JOIN ofic_usuario of_u ON o.num_oficina = of_u.num_oficina
            LEFT JOIN usuario u ON of_u.id_usuario = u.id_usuario
            GROUP BY o.nombre
            ORDER BY total_usuarios DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Datos de correlación: Solicitudes vs Usuarios por oficina
    public function obtenerDatosCorrelacion()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                COUNT(DISTINCT u.id_usuario) as total_usuarios,
                COUNT(DISTINCT s.id_solicitud) as total_solicitudes,
                COALESCE(SUM(ps.un_deseadas), 0) as total_productos
            FROM oficina o
            INNER JOIN ofic_usuario of_u ON o.num_oficina = of_u.num_oficina
            LEFT JOIN usuario u ON of_u.id_usuario = u.id_usuario
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            LEFT JOIN prod_solic ps ON s.id_solicitud = ps.id_solicitud
            GROUP BY o.nombre
            ORDER BY o.nombre";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // --- Métodos de Totales Generales ---

    public function obtenerTotalUsuarios()
    {
        $sql = "SELECT COUNT(*) as total FROM usuario";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_assoc()['total'] : 0;
    }

    public function obtenerTotalProductos()
    {
        $sql = "SELECT COUNT(*) as total FROM producto WHERE valido = 1";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_assoc()['total'] : 0;
    }

    public function obtenerTotalSolicitudes()
    {
        $sql = "SELECT COUNT(*) as total FROM solicitud";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_assoc()['total'] : 0;
    }

    public function obtenerTotalOficinas()
    {
        $sql = "SELECT COUNT(*) as total FROM oficina";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_assoc()['total'] : 0;
    }

    // --- Métodos de Estadísticas Adicionales ---

    // Productos más solicitados
    public function obtenerProductosMasSolicitados()
    {
        $sql = "SELECT 
                p.nombre as producto,
                p.medida,
                tp.nombre as tipo,
                SUM(ps.un_deseadas) as total_solicitado,
                COUNT(DISTINCT ps.id_solicitud) as veces_solicitado
            FROM producto p
            LEFT JOIN prod_solic ps ON p.id_producto = ps.id_producto
            LEFT JOIN tipo_prod tp ON p.id_tipo = tp.id_tipo
            WHERE p.valido = 1
            GROUP BY p.id_producto, p.nombre, p.medida, tp.nombre
            HAVING total_solicitado > 0
            ORDER BY total_solicitado DESC
            LIMIT 10";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Estados de solicitudes por mes
    public function obtenerEstadosSolicitudesPorMes($anio = null)
    {
        if ($anio === null) {
            $anio = date('Y');
        }

        $sql = "SELECT 
                DATE_FORMAT(s.fecha_solic, '%Y-%m') as mes,
                s.estado,
                COUNT(s.id_solicitud) as cantidad
            FROM solicitud s
            WHERE YEAR(s.fecha_solic) = ?
            GROUP BY DATE_FORMAT(s.fecha_solic, '%Y-%m'), s.estado
            ORDER BY mes, s.estado";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $anio);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $datos = [];

        while ($row = $resultado->fetch_assoc()) {
            $datos[] = $row;
        }

        $stmt->close();
        return $datos;
    }

    // Usuarios más activos (que hacen más solicitudes)
    public function obtenerUsuariosMasActivos()
    {
        $sql = "SELECT 
                u.nombre as usuario,
                u.cedula,
                r.nombre as rol,
                COUNT(s.id_solicitud) as total_solicitudes,
                SUM(ps.un_deseadas) as total_productos_solicitados
            FROM usuario u
            LEFT JOIN solicitud s ON u.id_usuario = s.id_solicitante
            LEFT JOIN prod_solic ps ON s.id_solicitud = ps.id_solicitud
            LEFT JOIN rol_usuario r ON u.id_cargo = r.id_cargo
            GROUP BY u.id_usuario, u.nombre, u.cedula, r.nombre
            HAVING total_solicitudes > 0
            ORDER BY total_solicitudes DESC
            LIMIT 10";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Proveedores más utilizados por tipo de producto
    public function obtenerProveedoresPorTipoProducto()
    {
        $sql = "SELECT 
                pr.nombre as proveedor,
                pr.rif,
                tp.nombre as tipo_producto,
                COUNT(prr.id_tipo) as veces_recomendado
            FROM proveedor pr
            INNER JOIN prov_recomendaciones prr ON pr.rif = prr.rif_proveedor
            INNER JOIN tipo_prod tp ON prr.id_tipo = tp.id_tipo
            WHERE pr.estado = 'Activo'
            GROUP BY pr.nombre, pr.rif, tp.nombre
            ORDER BY proveedor, veces_recomendado DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Solicitudes por director
    public function obtenerSolicitudesPorDirector()
    {
        $sql = "SELECT 
                d.nombre as director,
                d.ced_dir,
                o.nombre as oficina,
                COUNT(s.id_solicitud) as total_solicitudes,
                SUM(CASE WHEN s.estado = 'Aprobado' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN s.estado = 'Rechazado' THEN 1 ELSE 0 END) as rechazadas
            FROM director d
            LEFT JOIN oficina o ON d.ced_dir = o.ced_dir
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            GROUP BY d.nombre, d.ced_dir, o.nombre
            ORDER BY total_solicitudes DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Productos con registro de entrada (registro_prod)
    public function obtenerProductosConRegistroEntrada()
    {
        $sql = "SELECT 
                p.nombre as producto,
                p.medida,
                tp.nombre as tipo,
                COUNT(rp.id_solicitud) as veces_entregadas,
                SUM(rp.un_anadidas) as total_entregado
            FROM producto p
            LEFT JOIN prod_solic ps ON p.id_producto = ps.id_producto
            LEFT JOIN registro_prod rp ON ps.id_solicitud = rp.id_solicitud AND ps.num_linea = rp.num_linea
            LEFT JOIN tipo_prod tp ON p.id_tipo = tp.id_tipo
            WHERE p.valido = 1 AND rp.un_anadidas > 0
            GROUP BY p.id_producto, p.nombre, p.medida, tp.nombre
            ORDER BY total_entregado DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Solicitudes apeladas por oficina
    public function obtenerSolicitudesApeladas()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                COUNT(s.id_solicitud) as total_apeladas,
                SUM(CASE WHEN s.apelada = 1 THEN 1 ELSE 0 END) as apeladas_activas
            FROM oficina o
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            WHERE s.apelada = 1
            GROUP BY o.nombre
            HAVING total_apeladas > 0
            ORDER BY total_apeladas DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Total de notificaciones por tipo
    public function obtenerNotificacionesPorTipo()
    {
        $sql = "SELECT 
                tn.id_tipo_notif,
                tn.mensaje as tipo_notificacion,
                COUNT(n.id_notif) as total_notificaciones,
                COUNT(DISTINCT rn.id_usuario) as usuarios_notificados
            FROM tipo_notif tn
            LEFT JOIN notificacion n ON tn.id_tipo_notif = n.tipo
            LEFT JOIN receptor_notif rn ON n.id_notif = rn.id_notif
            GROUP BY tn.id_tipo_notif, tn.mensaje
            ORDER BY total_notificaciones DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Usuarios con acceso a múltiples oficinas
    public function obtenerUsuariosMultiplesOficinas()
    {
        $sql = "SELECT 
                u.nombre as usuario,
                u.cedula,
                r.nombre as rol,
                COUNT(DISTINCT of_u.num_oficina) as oficinas_acceso,
                GROUP_CONCAT(DISTINCT o.nombre SEPARATOR ', ') as lista_oficinas
            FROM usuario u
            INNER JOIN ofic_usuario of_u ON u.id_usuario = of_u.id_usuario
            INNER JOIN oficina o ON of_u.num_oficina = o.num_oficina
            LEFT JOIN rol_usuario r ON u.id_cargo = r.id_cargo
            GROUP BY u.id_usuario, u.nombre, u.cedula, r.nombre
            HAVING oficinas_acceso > 1
            ORDER BY oficinas_acceso DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Estadísticas de productos por tipo
    public function obtenerEstadisticasProductosPorTipo()
    {
        $sql = "SELECT 
                tp.nombre as tipo_producto,
                COUNT(p.id_producto) as total_productos,
                SUM(CASE WHEN p.valido = 1 THEN 1 ELSE 0 END) as productos_activos,
                SUM(CASE WHEN p.valido = 0 THEN 1 ELSE 0 END) as productos_inactivos,
                COUNT(DISTINCT prr.rif_proveedor) as proveedores_asociados
            FROM tipo_prod tp
            LEFT JOIN producto p ON tp.id_tipo = p.id_tipo
            LEFT JOIN prov_recomendaciones prr ON tp.id_tipo = prr.id_tipo
            GROUP BY tp.id_tipo, tp.nombre
            ORDER BY total_productos DESC";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Tiempo promedio de respuesta por oficina
    public function obtenerTiempoPromedioRespuesta()
    {
        $sql = "SELECT 
                o.nombre as oficina,
                AVG(DATEDIFF(rp.fecha_r, s.fecha_solic)) as dias_promedio_respuesta,
                COUNT(DISTINCT s.id_solicitud) as solicitudes_procesadas
            FROM oficina o
            LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
            LEFT JOIN prod_solic ps ON s.id_solicitud = ps.id_solicitud
            LEFT JOIN registro_prod rp ON ps.id_solicitud = rp.id_solicitud AND ps.num_linea = rp.num_linea
            WHERE rp.fecha_r IS NOT NULL AND s.fecha_solic IS NOT NULL
            GROUP BY o.nombre
            HAVING solicitudes_procesadas > 0
            ORDER BY dias_promedio_respuesta";

        $resultado = $this->db->query($sql);

        if (!$resultado) {
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}