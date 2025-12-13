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
        $id_usuario = $_SESSION['id'];
        $this->validarSesion();
        $titulo = 'Notificaciones';
        $this->eliminarRutinariamente($id_usuario);
        $notificacionesConUsuarios = $this->notif->obtenerDatos($id_usuario);

        require_once 'views/notificaciones/index.php';
    }
    public function eliminarNotif() {
        try{
            if(!isset($_POST['id_notif']) || !isset($_POST['id_usuario'])){
                echo json_encode(['success' => false, 'message' =>"ID de la notificacion o usuario no encontrado, ha surgido un error al capturar datos."]);
            }

            $id = $_POST['id_notif'];
            $id_usuario = $_POST['id_usuario'];
            if($this->notif->eliminarNotif($id, $id_usuario)){
                echo json_encode(['success'=> true, 'message' => "Notificación eliminada exitosamente"]);
            }else{
                echo json_encode(['success' => false, 'message' =>"Error al eliminar en la base de datos."]);
            }
        }
        catch(Exception $e){
                echo json_encode(['success' => false, 'message' =>"Error en el servidor."]);
        }
        exit();
    }
    public function eliminarRutinariamente($id_usuario)
    {
        try{
            if($this->notif->eliminarPorRutina($id_usuario)){
                return true;
            }else{
                error_log('No se han eliminado las notificaciones antiguas pues hubo un error en la base de datos');
                return false;
            }
        }
        catch(Exception $e){
                return false;
        }
    }
    public function leerNotif() {
        try{
            if(!isset($_POST['id_notif']) || !isset($_POST['id_usuario'])){
                echo json_encode(['success' => false, 'message' =>"ID de la notificacion o usuario no encontrado, ha surgido un error al capturar datos."]);
            }

            $id = $_POST['id_notif'];
            $id_usuario = $_POST['id_usuario'];
            if($this->notif->leerNotif($id, $id_usuario)){
                echo json_encode(['success'=> true, 'message' => "Notificación leida exitosamente"]);
            }else{
                echo json_encode(['success' => false, 'message' =>"Error al leer en la base de datos."]);
            }
        }
        catch(Exception $e){
                echo json_encode(['success' => false, 'message' =>"Error en el servidor."]);
        }
        exit();
    }
    public function leerTodas()
    {
        try{
            if(!isset($_POST['id_usuario'])){
                echo json_encode(['success' => false, 'message' =>"ID de la notificacion o usuario no encontrado, ha surgido un error al capturar datos."]);
            }

            $id_usuario = $_POST['id_usuario'];
            if($this->notif->leerTodaNotif($id_usuario)){
                echo json_encode(['success'=> true, 'message' => "¡Tu menú de notificaciones ha sido leido completamente!"]);
            }else{
                echo json_encode(['success' => false, 'message' =>"Error al leer en la base de datos."]);
            }
        }
        catch(Exception $e){
                echo json_encode(['success' => false, 'message' =>"Error en el servidor."]);
        }
        exit();
    }
    public function limpiarLeidas()
    {
        try{
            if(!isset($_POST['id_usuario'])){
                echo json_encode(['success' => false, 'message' =>"ID de la notificacion o usuario no encontrado, ha surgido un error al capturar datos."]);
            }

            $id_usuario = $_POST['id_usuario'];
            if($this->notif->limpiarNotifs($id_usuario)){
                echo json_encode(['success'=> true, 'message' => "¡Tu menú de notificaciones ha sido limpiado!"]);
            }else{
                echo json_encode(['success' => false, 'message' =>"Error al eliminar en la base de datos."]);
            }
        }
        catch(Exception $e){
                echo json_encode(['success' => false, 'message' =>"Error en el servidor."]);
        }
        exit();
    }
}