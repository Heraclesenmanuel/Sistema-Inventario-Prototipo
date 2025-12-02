<?php
require_once 'model/solicitudes.php';
class SolicitudesController extends AdminController
{
    private $solicitudes;
    public function __construct()
    {
        parent::__construct();
        $this->solicitudes = new Solicitud();
    }
    public function home() 
    {
        $this->validarSesion();
        $titulo = 'Solicitar';
        $oficinas = $this->solicitudes->cargarOficinas();
        $tipos_p = $this->solicitudes->getTipos();
        $solicitudes = $this->solicitudes->obtenerSolicitudes();
        $cant_solicts_no_en_rev = $this->solicitudes->contarSolictsNoEnRev($solicitudes);
        $productos = $this->getProductos();

        if (isset($_POST['departamento'])) {
            $this->agregarSolic();
        }
        require_once 'views/solicitudes/index.php';
    }
    public function agregarSolic()
    {
        // Sanitizar y validar datos antes de guardar
        $datosSolic = [
            'oficina' => trim($_POST['departamento'] ?? 'N/A'),
            'fecha_deseada' => trim($_POST['fecha_requerida'] ?? 'N/A'),
            'oficina_solic' => trim($_POST['departamento'] ?? 'N/A'),
            'comentarios' => trim($_POST['notas'] ?? 'N/A')
        ];
        $productos = $this->procesarProds();
        if (empty($datosSolic['oficina']) && empty($productos[0]['nombre'])){
            echo '<script>alert("El nombre del producto no puede estar vacío")</script>';
        } 
        else if ($this->solicitudes->guardarSolicitud($datosSolic) && $this->solicitudes->guardarProds($productos)) {
            header('Location: ?action=solicitudes&method=home');
            exit();
        } 
        else {
            echo '<script>alert("Error al guardar la solicitud. Intente nuevamente.")</script>';
        }
    }
    public function procesarProds() {
        $nombres = $_POST['nombre_producto'] ?? [];
        $un_deseadas = $_POST['cantidad'] ?? [];
        $tipos_p = $_POST['tipo_producto'] ?? [];
        $medidas = $_POST['unidad_medida'] ?? [];
        $productos = [];
        // Asegurarse de que sean arrays
        if (!is_array($nombres)) {
            return $productos;
        }
        
        for ($i = 0; $i < count($nombres); $i++) {
            // Validar que todos los campos existan
            if (!empty($nombres[$i]) && 
                isset($un_deseadas[$i]) && 
                isset($tipos_p[$i]) && 
                isset($medidas[$i])) {
                
                $productos[] = [
                    'nombre_producto' => trim($nombres[$i]),
                    'unidad_medida' => $medidas[$i],
                    'cantidad' => intval($un_deseadas[$i]),
                    'tipo_producto' => $tipos_p[$i]
                ];
            }
        }
        return $productos;
    }
    public function eliminarSolic() {
        try{
            if(!isset($_GET['id'])){
                echo '<script>alert("ID del producto no encontra a surguido un error")</script>';
            }

            $id = $_GET['id'];
            if($this->solicitudes->eliminarSolicitud($id)){
                echo '<script>alert("Producto eliminado exitosamente")</script>';
            }else{
                echo '<script>alert("Error al eliminar")</script>';
            }
        }
        catch(Exception $e){
            echo '<script>alert("Error en el servidor")</script>';
        }
        header('Location: ?action=solicitudes&method=home');
        exit();
    }
    public function cambiarEstado() {
        header('Content-Type: application/json');
        
        try {
            // Verificar si es una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido'
                ]);
                return;
            }
            // Obtener datos del POST
            $idSolicitud = $_POST['id_solicitud'] ?? null;
            $nuevoEstado = $_POST['nuevo_estado'] ?? null;
            $motivo = $_POST['motivo'] ?? '';
            
            // Validar datos requeridos
            if (!$idSolicitud || !$nuevoEstado) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
                return;
            }
            
            // Validar estado permitido
            $estadosPermitidos = ['Aprobado', 'Rechazado', 'En Revisión'];
            if (!in_array($nuevoEstado, $estadosPermitidos)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Estado no válido'
                ]);
                return;
            }
            $actualizado = $this->solicitudes->actualizarEstadoSolicitud($idSolicitud, $nuevoEstado, $motivo);

            if ($actualizado) {                
                echo json_encode([
                    'success' => true,
                    'message' => 'Solicitud colocada ' . strtolower($nuevoEstado) . ' correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo actualizar el estado de la solicitud'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function obtenerDetalles() {
        header('Content-Type: application/json');
        
        try {
            // Verificar si es una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido'
                ]);
                return;
            }
            
            // Obtener ID de la solicitud
            $idSolicitud = $_POST['id_solicitud'] ?? null;
            
            if (!$idSolicitud) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de solicitud no proporcionado'
                ]);
                return;
            }
            
            // Aquí llamas a tu modelo para obtener los detalles
            // Ejemplo:
            // $model = new SolicitudesModel();
            // $solicitud = $model->obtenerSolicitudPorId($idSolicitud);
            // $productos = $model->obtenerProductosSolicitud($idSolicitud);
            
            // Datos de ejemplo (reemplaza con tu lógica real)
            $solicitud = [
                'id_solicitud' => $idSolicitud,
                'nombre_solicitante' => 'Juan Pérez',
                'nombre_oficina' => 'Informática',
                'fecha_deseo' => '2024-01-20',
                'fecha_creacion' => '2024-01-15',
                'estado' => 'Pendiente',
                'notas' => 'Material necesario para el nuevo proyecto'
            ];
            
            $productos = [
                [
                    'nombre_producto' => 'Laptop Dell',
                    'cantidad' => 3,
                    'unidad_medida' => 'Unidades',
                    'tipo_producto' => 'Electrónicos'
                ],
                [
                    'nombre_producto' => 'Mouse inalámbrico',
                    'cantidad' => 5,
                    'unidad_medida' => 'Unidades',
                    'tipo_producto' => 'Electrónicos'
                ]
            ];
            
            $solicitud['productos'] = $productos;
            
            echo json_encode([
                'success' => true,
                'message' => 'Detalles obtenidos correctamente',
                'data' => $solicitud
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalles: ' . $e->getMessage()
            ]);
        }
    }
    public function obtenerProducto() {
        try {
            if(!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            $id = $_GET['id'];
            $producto = $this->solicitudes->obtenerSolicPorId($id);
            if($producto) {
                echo json_encode(['success' => true, 'producto' => $producto, 'message' => 'obtenido']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            }
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
        }
    }
    private function getProductos()
    {
        try {
            $productos = $this->solicitudes->cargarProds();
            if($productos) {
                return $productos;
            } else {
                return [];
            }
        } catch(Exception $e) {
            return [];
        }
    }
    public function actualizarSolic() {
        try {
            if(!isset($_POST['id'])) {
                echo '<script>alert("ID del producto no encontrado")</script>';
                return;
            }
            $datos = [
                'id_producto' => $_POST['id'],
                'codigo' => $_POST['productCode'],
                'nombre' => $_POST['nombre'],
                'medida' => $_POST['medida'],
                'un_disponibles' => $_POST['un_disponibles'],
                'tipo_p' => $_POST['tipo_p']
            ];
            if($this->solicitudes->actualizarSolic($datos)) {
                echo '<script>alert("Producto actualizado correctamente")</script>';
            } else {
                echo '<script>alert("Error al actualizar el producto")</script>';
            }
        } catch(Exception $e) {
            echo '<script>alert("Error en el servidor")</script>';
        }
        header('Location: ?action=solicitudes&method=home');
        exit();
    }
}