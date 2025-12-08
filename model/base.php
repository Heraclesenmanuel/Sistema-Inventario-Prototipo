<?php
class Base
{
    protected $db;

    public function __construct() {
        $this->db = (new BaseDatos())->conectar();
    }
    public function cargarOficinas() {
        $sql = "SELECT num_oficina, nombre FROM oficina";
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
        $sql = "SELECT id_tipo, nombre FROM tipo_prod";
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