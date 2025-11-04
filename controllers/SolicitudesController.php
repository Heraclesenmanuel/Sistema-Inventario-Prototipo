<?php

class SolicitudesController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
    }
    public function home() {
        $this->validarSesion();
        $titulo = 'Solicitar';
        require_once 'views/solicitudes/index.php';
    }
}