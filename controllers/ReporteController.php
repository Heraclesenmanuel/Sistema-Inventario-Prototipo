<?php
require_once 'model/reportes.php';

class ReporteController extends AdminController
{
    private $inventario;
    private $reportes;

    public function __construct()
    {
        parent::__construct();
        $this->inventario = new Inventario();
        $this->reportes = new Reportes();
    }
   //Funciones de estadisticas
   
    public function home(){
        //$this->validarSesion();
        $titulo = 'Estadisticas';
        $estadisticas = $this->inventario->obtenerEstadisticas();
        
        // Obtener datos para los nuevos gráficos
        $tiposProductosPorOficina = $this->reportes->obtenerTiposProductosPorOficina();
        $frecuenciaSolicitudes = $this->reportes->obtenerFrecuenciaSolicitudesPorOficina();
        $cantidadProductos = $this->reportes->obtenerCantidadProductosPorOficina();
        $solicitudesRechazadas = $this->reportes->obtenerSolicitudesRechazadasPorOficina();
        $usuariosPorOficina = $this->reportes->obtenerUsuariosPorOficina();
        $datosCorrelacion = $this->reportes->obtenerDatosCorrelacion();
        
        require_once 'views/estadisticas/index.php';
    }
}