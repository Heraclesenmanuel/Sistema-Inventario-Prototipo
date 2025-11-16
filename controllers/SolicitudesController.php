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
        $solicts_no_en_rev = $this->solicitudes->contarSolictsNoEnRev($solicitudes);

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
            echo '<script>alert("Error al guardar el producto. Intente nuevamente.")</script>';
        }
    }
    public function procesarProds() {
        $nombres = $_POST['nombre_producto'] ?? [];
        $un_deseadas = $_POST['cantidad'] ?? [];
        $tipos_p = $_POST['tipo_producto'] ?? [];
        $medidas = $_POST['unidad_medida'] ?? [];
        echo var_dump($nombres, $un_deseadas, $tipos_p, $medidas);
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