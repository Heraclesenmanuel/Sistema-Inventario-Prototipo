<?php
class InventarioController extends AdminController
{
    private $inventario;

    public function __construct()
    {
        parent::__construct();
        $this->inventario = new Inventario();
    }
     //FUNCIONES DEL INVETARIO
    public function home() {
        //$this->validarSesion();
        $titulo = 'Inventario';
        $tipos_p = $this->inventario->getTipos();
        $datosInven = $this->inventario->obtenerDatos();

        if (isset($_POST['add'])) {
            $this->agregarProducto();
        }
        require_once 'views/inventario/index.php';
    }
    public function agregarProducto()
    {
        // Sanitizar y validar datos antes de guardar
        $datos = [
            'nombre' => trim($_POST['productName'] ?? 'N/A'),
            'tipo_p' => ($_POST['tipo_p']),
            'medida' => trim($_POST['productMeasure'] ?? 'N/A')
        ];

        if (empty($datos['nombre'])){
            echo '<script>alert("El código del producto no puede estar vacío")</script>';
        } 
        else if ($this->inventario->guardarDatos($datos)) {
            header('Location: ?action=inventario&method=home&mensaje=exito'); //implementar mensaje exito
            exit();
        } 
        else {
            echo '<script>alert("Error al guardar el producto. Intente nuevamente.")</script>';
        }
    }
    public function categorias()
    {
        //$this->validarSesion();
        $titulo = 'Categorias';
        $categorias = $this->inventario->getTipos();
            if (isset($_POST['add'])) {
            // Sanitizar y validar datos antes de guardar
            $datos = [
                'nombre' => trim($_POST['productName'] ?? 'N/A'),
            ];

            if (empty($datos['nombre'])){
                echo '<script>alert("El código de la categoria no puede estar vacío")</script>';
            } 
            else if ($this->inventario->guardarCategoria($datos)) {
                header('Location: ?action=inventario&method=categorias&mensaje=exito'); //implementar mensaje exito
                exit();
            } 
            else {
                echo '<script>alert("Error al guardar la categoria. Intente nuevamente.")</script>';
            }
        }
        require_once 'views/inventario/categorias.php';
    }
    public function eliminarProducto() {
        try{
            if(!isset($_GET['id'])){
                echo '<script>alert("ID del producto no encontrado, ha surguido un error")</script>';
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
    public function actualizarProducto() {
        try {
            if(!isset($_POST['editId'])) {
                echo '<script>alert("ID del producto no encontrado")</script>';
                return;
            }
            $datos = [
                'id_producto' => $_POST['editId'],
                'nombre' => $_POST['nombre'],
                'medida' => $_POST['editMeasure'],
                'tipo_p' => $_POST['editTipo']
            ];
            if($this->inventario->actualizarProducto($datos)) {
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
    public function obtenerCategoria() {
        try {
            if(!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            }

            $id = $_GET['id'];
            $categoria = $this->inventario->obtenerCategoriaPorId($id);
            if($categoria) {
                echo json_encode(['success' => true, 'producto' => $categoria, 'message' => 'obtenido']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Categoria no encontrado']);
            }
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
        }
    }
    public function actualizarCategoria() {
        try {
            if(!isset($_POST['editId'])) {
                echo '<script>alert("ID de categoria no encontrada")</script>';
                return;
            }
            $datos = [
                'id_tipo' => $_POST['editId'],
                'nombre' => $_POST['nombre']
            ];
            if($this->inventario->actualizarCategoria($datos['nombre'], $datos['id_tipo'])) {
                echo '<script>alert("Categoria actualizada correctamente")</script>';
            } else {
                echo '<script>alert("Error al actualizar la categoria")</script>';
            }
        } catch(Exception $e) {
            echo '<script>alert("Error en el servidor")</script>';
        }
        header('Location: ?action=inventario&method=categorias');
        exit();
    }
    public function eliminarCategoria() {
        try{
            if(!isset($_GET['id'])){
                echo '<script>alert("ID de la categoria no encontrada, ha surgido un error")</script>';
            }

            $id = $_GET['id'];
            if($this->inventario->eliminarCategoria($id)){
                echo '<script>alert("Categoria eliminada exitosamente")</script>';
            }else{
                echo '<script>alert("Error al eliminar")</script>';
            }
        }
        catch(Exception $e){
            echo '<script>alert("Error en el servidor")</script>';
        }
        header('Location: ?action=inventario&method=categorias');
        exit();
    }
}