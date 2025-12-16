<?php
class Base
{
    protected $db;

    public function __construct() {
        $this->db = (new BaseDatos())->conectar();
    }
    public function cargarOficinas() {
        $sql_extra = "WHERE of_u.id_usuario = " . $_SESSION['id'];
        if($_SESSION['dpto'] == 3 || $_SESSION['dpto'] == 4)
        {
            $sql_extra = 'GROUP BY o.nombre';
        }
        $sql = "SELECT of_u.num_oficina, nombre 
                FROM ofic_usuario of_u
                INNER JOIN oficina o ON o.num_oficina=of_u.num_oficina
                " . $sql_extra;
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
    public function cargarOfichinas() {
        $sql = "SELECT of_u.num_oficina, nombre 
                FROM ofic_usuario of_u
                INNER JOIN oficina o ON o.num_oficina=of_u.num_oficina
                GROUP BY o.nombre";
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
    public function getRecomendaciones($rif)
    {
        $sql = "SELECT tp.nombre as nombre
        FROM prov_recomendaciones pr INNER JOIN tipo_prod tp ON pr.id_tipo=tp.id_tipo 
        WHERE rif_proveedor = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $rif);
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
    public function getTipos() {
        $sql = "SELECT tp.id_tipo,
       tp.nombre,
       COALESCE(SUM(ps.un_deseadas), 0) AS cant_solic,
       COALESCE(SUM(rp.un_anadidas), 0) AS cant_pend
FROM tipo_prod tp
LEFT JOIN producto p
       ON tp.id_tipo = p.id_tipo
LEFT JOIN prod_solic ps
       ON p.id_producto = ps.id_producto
LEFT JOIN registro_prod rp
       ON ps.id_solicitud = rp.id_solicitud
      AND ps.num_linea   = rp.num_linea
GROUP BY tp.id_tipo, tp.nombre;
;
";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $tipos = [];

        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }

        return [
            'data' => $tipos,
            'success' => true
        ];
    }
    public function getRelacionesProvTipo() {
        $sql = "SELECT * FROM prov_recomendaciones";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $relaciones = [];

        while ($row = $result->fetch_assoc()) {
            $relaciones[] = $row;
        }

        return [
            'data' => $relaciones,
            'success' => true
        ];
    }
        public function getRoles() {
        $sql = "SELECT * FROM rol_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];

        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }

        return [
            'data' => $roles,
            'success' => true
        ];
    }
    public function getDirectores() {
        $sql = "SELECT ced_dir, nombre, telf FROM director";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $tipos = [];

        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }

        return [
            'data' => $tipos,
            'success' => true
        ];
    }
}