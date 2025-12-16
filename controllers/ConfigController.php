    <?php
    class ConfigController extends AdminController
    {
        protected $config;
        public function __construct()
        {
            parent::__construct();
            $this->config = new Config();
        }
        //Funcion de configuracion
        public function home(){
            $this->validarSesion();
            $titulo = 'Configuracion';
            $roles = $this->config->getRoles();
            $usuarios = $this->config->mostrarUsuarios();
            $oficinas = $this->config->cargarOfichinas();

            require_once 'views/conf/index.php';
        }
        public function getOficinasUsuario()
        {
            $id_usuario = isset($_POST['id']) ? (int)trim($_POST['id']) : '';
            try {
                $resultado = $this->config->getOficinasUsuario($id_usuario);
                if (!$resultado && !is_array($resultado))
                {
                    header('Content-Type: application/json');
                    echo json_encode([
                    'success' => false,
                    'message' => "No se ha podido ejecutar la consulta de recomendaciones",
                    ]);
                    exit();
                }
                else
                {
                    header('Content-Type: application/json');
                    echo json_encode([
                            'success' => true,
                            'message' => 'Recomendaciones obtenidas correctamente',
                            'recomendaciones' => $resultado
                        ]);
                    exit();
                }
            }
            catch (Exception $e)
            {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit();
            }
        }
        public function cambiarClave()
        {
            if (isset($_POST['bandera_cambiar_clave']))
            {
                $clave_en_bd = $this->getM();
                $actual = trim($_POST['clave_actual']);
                $nueva = trim($_POST['clave_nueva']);
                $confirmar = trim($_POST['confirmar_clave']);

                // Validar que las claves coincidan
                if ($nueva !== $confirmar) {
                    $_SESSION['mensaje'] = 'Las claves no coinciden';
                    $_SESSION['tipo_mensaje'] = 'error';
                    header('Location: ?action=config&method=home');
                    exit();
                }

                // Verificar la clave actual con la constante APP_Password
                if ($actual !== $clave_en_bd['claveSuper']) {
                    $_SESSION['mensaje'] = 'La clave actual es incorrecta ' . $clave_en_bd['claveSuper'];
                    $_SESSION['tipo_mensaje'] = 'error';
                    header('Location: ?action=config&method=home');
                    exit();
                }

                // Actualizar la clave
                $resultado = $this->config->updateClave($nueva);

                $_SESSION['mensaje'] = $resultado['message'];
                $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'error';

                header('Location: ?action=config&method=home');
                exit();
            }
        }
        public function agregarUsuario()
        {
         // Agregar nuevo usuario administrativo
            if(isset($_POST['bandera_agregar_usuario'])){
                $nombre = trim($_POST['nombre_usuario']);
                $cedula = trim($_POST['cedula']);
                $clave = trim($_POST['clave_usuario']);
                $correo = trim($_POST['correo']);
                $id_cargo = (int)$_POST['id_cargo'];
                $oficinas = json_decode($_POST['oficinas_seleccionadas'], true);
                $clave_super = null;
                if(isset($_POST['clave_Super']))
                {
                    $clave_super = trim($_POST['clave_Super']);
                }

                $resultado = $this->config->addUsuario($cedula, $nombre, $clave, $id_cargo, $correo, $oficinas, $claveSuper=$clave_super);

                $_SESSION['mensaje'] = $resultado['message'];
                $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'error';
                
                header('Location: ?action=config&method=home');
                exit();
            }
        }
        public function eliminarUsuario()
        {
            $id_usuario = (int)$_POST['id_usuario'];
                
            $resultado = $this->config->deleteUsuario($id_usuario);
                
            $_SESSION['mensaje'] = $resultado['message'];
            $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'error';
                
            header('Location: ?action=config&method=home');
            exit();
        }
        public function cambiarNombreApp() {
            $this->iniciarSesion();
            if(isset($_POST['nombre_app'])) {
                $nombre = trim($_POST['nombre_app']);
                
                // Validación básica: no vacío y longitud razonable
                if(empty($nombre) || strlen($nombre) > 100){
                    $_SESSION['mensaje'] = 'El nombre de la aplicación no puede estar vacío ni exceder 100 caracteres.';
                    $_SESSION['tipo_mensaje'] = 'error';
                    header('Location: ?action=config&method=home');
                    exit();
                }
                
                $resultado = $this->config->updateNombre($nombre);

                if(isset($resultado['success']) && $resultado['success']) {
                    $_SESSION['mensaje'] = $resultado['message'];
                    $_SESSION['tipo_mensaje'] = 'success';
                } else {
                    $_SESSION['mensaje'] = isset($resultado['message']) ? $resultado['message'] : 'Error al actualizar el nombre.';
                    $_SESSION['tipo_mensaje'] = 'error';
                }
                header('Location: ?action=config&method=home');
                exit();
                
            } else {
                $_SESSION['mensaje'] = 'No se recibieron datos';
                $_SESSION['tipo_mensaje'] = 'error';
                header('Location: ?action=config&method=home');
                exit();
            }
        }
        public function verif()
        {
            try 
            {
                $clave = $this->config->verif();
                if($clave) {
                    echo json_encode(['success' => true, 'claveSeguridad' => $clave, 'message' => 'obtenido']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Clave no encontrada']);
                }
            }   
            catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
            }
        }
        public function getM()
        {
            try 
            {
                $clave = $this->config->verif();
                if($clave) {
                    return $clave;
                } else {
                   error_log('Clave no encontrada');
                   return false;
                }
            }   
            catch(Exception $e) {
                error_log('Error en el servidor');
                return false;
        }
    }
}