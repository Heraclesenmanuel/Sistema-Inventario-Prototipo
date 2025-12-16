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
   
    public function home()
    {
        $this->validarSesion();
        $titulo = 'Estadísticas del Sistema';
        
        // Obtener datos de los métodos del modelo
        $tiposProductosPorOficina = $this->reportes->obtenerTiposProductosPorOficina();
        $frecuenciaSolicitudes = $this->reportes->obtenerFrecuenciaSolicitudesPorOficina();
        $cantidadProductos = $this->reportes->obtenerCantidadProductosPorOficina();
        $solicitudesRechazadas = $this->reportes->obtenerSolicitudesRechazadasPorOficina();
        $usuariosPorOficina = $this->reportes->obtenerUsuariosPorOficina();
        $datosCorrelacion = $this->reportes->obtenerDatosCorrelacion();
        
        // Obtener totales
        $totales = [
            'usuarios' => $this->reportes->obtenerTotalUsuarios(),
            'productos' => $this->reportes->obtenerTotalProductos(),
            'solicitudes' => $this->reportes->obtenerTotalSolicitudes(),
            'oficinas' => $this->reportes->obtenerTotalOficinas()
        ];
        
        // Si necesitas datos del gráfico original de estado, puedes obtener productos por tipo
        $estadisticas['por_estado'] = $this->reportes->obtenerEstadisticasProductosPorTipo();
        // Cargar la vista con todos los datos
        require_once 'views/estadisticas/index.php';
    }
}