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

        $datosSolicits = $this->solicitudes->obtenerDatos();

        if (isset($_POST['requestId'])) {
            // Sanitizar y validar datos antes de guardar
            $datos = [
                'codigo' => trim($_POST['oficina'] ?? 'N/A'),
                'nombre' => trim($_POST['productName'] ?? 'N/A'),
                'un_disponibles' => (int)(trim($_POST['requestQuantity'] ?? 0)),
                'tipo_p' => trim(($_POST['tipo_producto'] ?? 'N/A')),
                'medida' => trim($_POST['productMeasure'] ?? 'N/A'),
                'fecha_deseada' => trim($_POST['fecha_requerida'] ?? 'N/A'),
                'oficina_solic' => trim($_SESSION['dpto'] ?? 'N/A'),
                'comentarios' => trim($_POST['notas'] ?? 'N/A')
            ];
            if (empty($datos['oficina'])){
                echo '<script>alert("El código del producto no puede estar vacío")</script>';
            } 
            else if ($this->solicitudes->guardarSolicitud($datos)) {
                header('Location: ?action=solicitudes&method=home');
                exit();
            } 
            else {
                echo '<script>alert("Error al guardar el producto. Intente nuevamente.")</script>';
            }
        }
        require_once 'views/solicitudes/index.php';
    }
    
    public function eliminarProducto() {
        try{
            if(!isset($_GET['id'])){
                echo '<script>alert("ID del producto no encontra a surguido un error")</script>';
            }

            $id = $_GET['id'];
            if($this->solicitudes->eliminarDatos($id)){
                echo '<script>alert("Producto eliminado exitosamente")</script>';
            }else{
                echo '<script>alert("Error al eliminar")</script>';
            }
        }
        catch(Exception $e){
            echo '<script>alert("Error en el servidor")</script>';
        }
        header('Location: ?action=inventario&method=home');
        exit();
    }
    public function obtenerProducto() {
        try {
            if(!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            $id = $_GET['id'];
            $producto = $this->solicitudes->obtenerProductoPorId($id);
            if($producto) {
                echo json_encode(['success' => true, 'producto' => $producto, 'message' => 'obtenido']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            }
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
        }
    }
    public function actualizarProducto() {
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
            if($this->solicitudes->actualizarProducto($datos)) {
                echo '<script>alert("Producto actualizado correctamente")</script>';
            } else {
                echo '<script>alert("Error al actualizar el producto")</script>';
            }
        } catch(Exception $e) {
            echo '<script>alert("Error en el servidor")</script>';
        }
        header('Location: ?action=inventario&method=home');
        exit();
    }
}