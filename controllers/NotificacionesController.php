<?php
require_once __DIR__ . '/../model/Notificaciones.php';
class NotificacionesController extends AdminController
{
    private $notif;

    public function __construct()
    {
        parent::__construct();
        $this->notif = new Notificaciones();
    }
     //FUNCIONES DE NOTIF
    public function home() {
        $this->validarSesion();
        $titulo = 'Notificaciones';
        $datosInven = $this->notif->obtenerDatos();

        if (isset($_POST['add'])) {
            // Sanitizar y validar datos antes de guardar
            $datos = [
                'codigo' => trim($_POST['productCode'] ?? 'N/A'),
                'nombre' => trim($_POST['productName'] ?? 'N/A'),
                'un_disponibles' => (int)(trim($_POST['productStock'] ?? 0)),
                'tipo_p' => ($_POST['tipo_p']),
                'medida' => trim($_POST['productMeasure'] ?? 'N/A')
            ];

            if (empty($datos['codigo'])){
                echo '<script>alert("El código del producto no puede estar vacío")</script>';
            } 
            else if ($this->inventario->guardarDatos($datos)) {
                header('Location: ?action=notifiacciones&method=home&mensaje=exito');
                exit();
            } 
            else {
                echo '<script>alert("Error al guardar el producto. Intente nuevamente.")</script>';
            }
        }
        require_once 'views/notificaciones/index.php';
    }
    public function eliminarProducto() {
        try{
            if(!isset($_GET['id'])){
                echo '<script>alert("ID del producto no encontra a surguido un error")</script>';
            }

            $id = $_GET['id'];
            if($this->inventario->eliminarDatos($id)){
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
            $producto = $this->inventario->obtenerProductoPorId($id);
            if($producto) {
                echo json_encode(['success' => true, 'producto' => $producto, 'message' => 'obtenido']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            }
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
        }
    }
}