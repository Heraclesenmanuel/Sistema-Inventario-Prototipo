<?php
//Esto es de OFICINAS. Solo que sigue llamando clientes internamente
class OficinasController extends AdminController
{
    protected $oficinas;
// Función de Oficina
    public function __construct()
    {
        parent::__construct();
        $this->oficinas = new Oficina();
    }
    public function home(){
        $this->validarSesion();
        $titulo = 'Oficinas';
        $oficinas = $this->oficinas->getOficinas();
        $directores = $this->oficinas->getDirectores();

        if (isset($_POST['btn-add'])) {
            $this->agregarOficina();
            header('Location: ?action=oficinas&method=home&exito=1');
        }
        require_once 'views/oficinas/index.php';
    }
    public function directores()
    {
        $this->validarSesion();
        $titulo = 'Directores';
        $directores = $this->oficinas->getDirectores();
        require_once 'views/oficinas/directores.php';
    }
    public function agregarOficina()
    {
        $numero = $_POST['num_oficina'];
        $nombre = trim($_POST['name']);
        $cedula = trim($_POST['cedula']);
        $telefono = trim($_POST['cel']);

        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($cedula)) {
            echo '<script>alert("Campos requeridos, El nombre y la cédula son obligatorios")</script>';
        } 
        else {
            $this->agregarDirector($cedula);
            if ($this->oficinas->agregarOficina($numero, $nombre, $cedula, $telefono)) {
                echo '<script>alert("¡Éxito! Oficina agregada correctamente")
                    </script>';
                header('Location:?action=oficinas&method=home');
                }
            else {
                echo '<script>
                alert("Error al agregar la oficina. Intente nuevamente.")
                </script>';
            }
        }
    }
    public function capturarDirector()
    {
        $cedula = trim($_POST['cedula']);
        echo $this->agregarDirector($cedula);
        exit();
    }
    public function agregarDirector($cedula)
    {
        $nombre = trim($_POST['dir_nombre']);
        $telf = trim($_POST['dir_telf']);
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($telf)) {
            return json_encode([
                'success' => false,
                'message' => 'El nombre y telefono deben estar llenos.'
            ]);
        }
        else {
            if ($this->oficinas->agregarDir($nombre, $cedula, $telf)) {
                    return json_encode([
                            'success' => true,
                            'message' => 'Director agregado correctamente'
                        ]);
                }
                else
                { 
                    return json_encode([
                        'success' => false,
                        'message' => 'Asegurese que su Cedula no se repita entre directores.'
                ]);
                }
        }
    }
    public function deleteOficina(){
        $id = $_GET['id'];
        try {
            // Verificar que el ID exista en la petición
            if (empty($id)) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "ID de la oficina no encontrada",
                        confirmButtonColor: "#e74c3c"
                    }).then(() => {
                        window.location.href = "?action=oficinas&method=users";
                    });
                </script>';
                exit();
            }

            $id = intval($_GET['id']); // Convertir a entero para seguridad
            // Intentar eliminar el cliente
            if ($this->oficinas->deleteOficina($id)) {
                echo json_encode((["success" => true]));
            } else {
                echo json_encode(["success" => false, "error" => "No se pudo eliminar la oficina. Verifique que exista."]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => "Ocurrió un error inesperado: ' . $e->getMessage() . '"]);
        }
        exit();
    }

}