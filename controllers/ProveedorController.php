<?php
class ProveedorController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
    }
//Funciones de Proveedores
    public function home(){
        $this->iniciarSesion();
        $titulo = 'Proveedores';
        $categorias = $this->proveedores->getTipos();
        $proveedores = $this->proveedores->obtenerProveedores();
        require_once 'views/provedor/index.php';
    }
    public function eliminarProveedor() {

        try {
            header('Content-Type: application/json; charset=utf-8');
            
            // Obtener el ID desde POST o GET
            $rif = null;
            $rif = $_POST['rif'];
            //$rif = intval($_GET['rif']);
            
            if (!$rif) {
                throw new Exception('ID del proveedor es requerido');
            }
            
            // Verificar que el proveedor existe
            $proveedorExiste = $this->proveedores->obtenerProveedorPorId($rif);
            if (!$proveedorExiste) {
                throw new Exception('El proveedor no existe');
            }
            
            // Eliminar el proveedor
            $resultado = $this->proveedores->eliminarProveedor($rif);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Proveedor eliminado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception("Error al eliminar el proveedor de la base de datos");
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        
        exit;
    }

    public function addProveedor() {
        try {
            // Forzar respuesta en JSON
            header('Content-Type: application/json; charset=utf-8');
            
            // Verificar que la solicitud sea POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar campos requeridos (usando el nombre del formulario 'nombre')
            $requiredFields = ['nombre', 'email', 'telefono', 'nombre', 'estado', 'rif'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo {$field} es requerido");
                }
            }
            $rif = trim($_POST['rif']);
            if($this->proveedores->chequearExistenciaRif($rif))
            {
                // Obtener y sanitizar los datos
                $data = [
                    'nombre_proveedor' => trim($_POST['nombre']), // ✅ Del form 'nombre' a DB 'nombre_proveedor'
                    'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
                    'telefono' => trim($_POST['telefono']),
                    'estado' => trim($_POST['estado']),
                    'direccion' => isset($_POST['direccion']) ? trim($_POST['direccion']) : '',
                    'nota' => isset($_POST['nota']) ? trim($_POST['nota']) : '',
                    'rif' => $rif,
                    'categorias_recomendadas' => isset($_POST['categorias_seleccionadas']) ? json_decode($_POST['categorias_seleccionadas']) : []
                ];

                // Validar formato de email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El formato del email no es válido");
                }

                // Insertar en la base de datos
                $resultado = $this->proveedores->agregarProveedor($data);

                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Proveedor agregado correctamente'
                    ]);
                } else {
                    throw new Exception("Error al insertar en la base de datos");
                }
            }
            else 
            {
                echo json_encode([
                        'success' => false,
                        'message' => 'Asegurese que su RIF no se repita entre proveedores.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }

    public function updateProveedor() {
        try {
            header('Content-Type: application/json; charset=utf-8');
            $rif = trim($_POST['rif']);
            $rif_og = trim($_POST['rif_original']);
            if($this->proveedores->chequearConvergenciaRif($rif, $rif_og))
            {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
                }
                
                if (empty($rif)) {
                    throw new Exception('RIF del proveedor es requerido');
                }
                $requiredFields = ['nombre', 'email', 'telefono', 'nombre', 'estado', 'rif', 'rif_original'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("El campo {$field} es requerido");
                    }
                }
                
                $data = [
                    'nombre_proveedor' => trim($_POST['nombre']),
                    'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
                    'telefono' => trim($_POST['telefono']),
                    'rif' => $rif,
                    'estado' => trim($_POST['estado']),
                    'direccion' => isset($_POST['direccion']) ? trim($_POST['direccion']) : '',
                    'nota' => isset($_POST['nota']) ? trim($_POST['nota']) : '',
                    'rif_original' => $rif_og,
                    'categorias_recomendadas' => isset($_POST['categorias_seleccionadas']) ? json_decode($_POST['categorias_seleccionadas']) : []
                ];
                
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El formato del email no es válido");
                }
                
                $resultado = $this->proveedores->actualizarProveedor($data);
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Proveedor actualizado correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar en la base de datos");
                }
            }
            else 
            {
                echo json_encode([
                        'success' => false,
                        'message' => 'Asegurese que su RIF no se repita entre proveedores.'
                    ]);
            }
            
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }
    public function getRecomendaciones()
    {
        $rif = isset($_POST['rif']) ? trim($_POST['rif']) : '';
        try {
            $resultado = $this->proveedores->getRecomendaciones($rif);
            if (!$resultado && !is_array($resultado))
            {
                echo json_encode([
                'success' => false,
                'message' => "No se ha podido ejecutar la consulta de recomendaciones",
            ]);
            }
            else
            {
                echo json_encode([
                        'success' => true,
                        'message' => 'Recomendaciones obtenidas correctamente',
                        'recomendaciones' => $resultado
                    ]);
            }
        }
        catch (Exception $e)
        {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}