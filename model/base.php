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