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
        $notificacionesConUsuarios = $this->notif->obtenerDatos(1);

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