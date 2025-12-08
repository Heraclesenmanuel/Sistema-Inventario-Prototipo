<?php
require_once __DIR__ . '/../model/Conexion.php';
require_once __DIR__ . '/../model/Inicio.php';
class InicioController
{
    public $modelo;
    public function __construct()
    {
        $this->modelo = new Inicio();
    }
    public function inicio() {
        require_once 'views/home/inicio.php';
    }
    public function home() {
        require_once 'views/home/index.php';
    }
    public function login() {
        require_once 'views/auth/login.php';
    }
    public function loginAuthenticate() {
        if(isset($_POST['init'])){
            if(strlen($_POST['user']) >= 3 && strlen($_POST['password']) >= 3) {
                $usuario = trim($_POST['user']);
                $password = trim($_POST['password']);
                $this->modelo->loginAuthenticate($usuario, $password);
            }
            else{
                echo "
                <script>alert('Los campos debe ser mayores a 3 digitos');
                setTimeout(()=>{
                    window.location.href = './'
                },500)
                </script>";
            }
        }
    }
    public function forgotPassw()
    {
        require_once 'views/auth/forgot.php';
    }
    public function sendPasswRequest()
    {
        $correo = trim($_POST['user']);
        $this->modelo->checkPasswRequest($correo);
    }
    public function confirmCode()
    {
        require_once 'views/auth/confirmcode.php';
    }
    public function checkCode() {
        if(isset($_POST['codigo'])){
            if(strlen($_POST['codigo']) >= 32) {
                $codigo = trim($_POST['codigo']);
                $this->modelo->chequearCodigo($codigo);
            }
            else{
                echo "
                <script>alert('El codigo de recuperacion debe ser de 32 digitos');
                setTimeout(()=>{
                    window.location.href = './'
                },500)
                </script>";
            }
        }
    }
    public function changePassw() {
        require 'views/auth/changep.php';
    }
    public function submitNewPassw()
    {
        if(isset($_POST['init'])){
            if(strlen($_POST['passw']) >= 3 && strlen($_POST['passw2']) >= 3) {
                $password1 = trim($_POST['passw']);
                $password2 = trim($_POST['passw2']);
                $this->modelo->changePassw($password1, $password2, $_SESSION['id']);
            }
            else{
                echo "
                <script>alert('Las contraseñas deben de ser mayores a 3 digitos');
                setTimeout(()=>{
                    window.location.href = './'
                },500)
                </script>";
            }
        }
    }
}