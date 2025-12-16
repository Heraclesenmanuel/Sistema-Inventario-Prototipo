<?php
require_once 'model/Conexion.php';
require_once 'model/Inicio.php';
require_once 'model/inventario.php';
require_once 'model/config.php';
require_once 'model/oficinas.php';
require_once 'model/proveedores.php';

class AdminController
{
    protected $bdatos;
    protected $pos;
    protected $proveedores;
    public function __construct()
    {
        $this->bdatos = new Inicio();
        $this->proveedores = new Proveedores();
        $this->validarSesion();

    }
    protected function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', 1800);
            session_set_cookie_params(1800);
            session_start();
        }
    }
    public function cerrar()
    {
        session_unset();
        session_destroy();
        header("Location: ./");
        exit();
    }
    protected function validarSesion()
    {
        $this->iniciarSesion();
        if (!isset($_SESSION['nombre'])) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "warning",
                        title: "Acceso denegado",
                        text: "Por favor, inicie sesión para continuar.",
                        confirmButtonColor: "#e74c3c"
                    }).then(() => {
                        window.location.href = "./";
                    });
                });
            </script>';
            exit();
        }
        // Verificar si el usuario existe en la base de datos
        else {
            $usuario = $this->bdatos->obtenerNombreUsuario($_SESSION['id'], true);
            if (!$usuario) {
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Usuario no existe",
                        text: "El usuario actual ha sido eliminado. Por favor, inicie sesión con otro usuario.",
                        confirmButtonColor: "#e74c3c"
                    }).then(() => {
                        window.location.href = "./";
                    });
                });
            </script>';
                session_destroy();
                exit();
            }
        }
    }
    public function home()
    {
        //$this->validarSesion();

        if (isset($_POST['uptade'])) {
            $precio = trim($_POST['dollar']);
            //$resultado = (new Config())->updateDollar($precio);

            //$_SESSION['mensaje'] = $resultado['message'];
            // $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'error';

            header('Location: ?action=admin');
            exit();
        }

        require_once 'views/home/admin.php';
    }
}