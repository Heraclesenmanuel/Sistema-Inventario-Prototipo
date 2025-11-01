<?php
class ReporteController extends AdminController
{
    private $inventario;

    public function __construct()
    {
        parent::__construct();
        $this->inventario = new Inventario();
    }
   //Funciones de estadisticas
    public function home(){
        $this->validarSesion();
        $titulo = 'Estadisticas';
        $estadisticas = $this->inventario->obtenerEstadisticas();
        require_once 'views/estadisticas/index.php';
    }
}