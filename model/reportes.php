<?php
require_once 'model/base.php';

class Reportes extends Base {
    
    // Tipos de productos solicitados por oficina
    public function obtenerTiposProductosPorOficina() {
        $sql = "SELECT 
                    o.nombre as oficina,
                    tp.nombre as tipo_producto,
                    COUNT(DISTINCT ps.id_solicitud) as cantidad
                FROM oficina o
                LEFT JOIN solicitud s ON o.num_oficina = s.num_oficina
                LEFT JOIN prod_solic ps ON s.id_solicitud = ps.id_solicitud
                LEFT JOIN producto p ON ps.id_producto=p.id_producto
                LEFT JOIN tipo_prod tp ON tp.id_tipo = p.id_tipo
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
    public function obtenerFrecuenciaSolicitudesPorOficina() {
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
    public function obtenerCantidadProductosPorOficina() {
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
    public function obtenerSolicitudesRechazadasPorOficina() {
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
    public function obtenerUsuariosPorOficina() {
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
    public function obtenerDatosCorrelacion() {
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
}