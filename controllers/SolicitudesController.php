<?php
require_once 'model/solicitudes.php';
class SolicitudesController extends AdminController
{
    private $solicitudes;
    public function __construct()
    {
        parent::__construct();
        $this->solicitudes = new Solicitud();
    }
    public function home()
    {
        //$this->validarSesion();
        $titulo = 'Solicitar';
        $oficinas = $this->solicitudes->cargarOficinas();
        $tiposProducto = $this->solicitudes->getTipos();
        $solicitudes = $this->solicitudes->obtenerSolicitudes();
        $productos = $this->getProductos();

        if (isset($_POST['departamento'])) {
            if ($this->agregarSolic()) {
                header('Location: ?action=solicitudes&method=home');
            } else {
                header('Location: ?action=solicitudes&method=home&error=1');
            }
        }
        if ($_SESSION['dpto'] == 4) {
            $proveedores = $this->proveedores->obtenerProveedores();
            $relacionesProvTipo = $this->proveedores->getRelacionesProvTipo();
            $solicitudes = $this->obtenerDetallesMultiples($solicitudes);
            require_once 'views/solicitudes/movimientos.php';
        } else {
            $cant_solicts_no_en_rev = $this->solicitudes->contarSolictsNoEnRev($solicitudes);
            require_once 'views/solicitudes/index.php';
        }
    }
    public function procesarProds()
    {
        $ids_producto = $_POST['producto_id'] ?? [];
        $nombres = $_POST['nombre_producto'] ?? [];
        $un_deseadas = $_POST['cantidad'] ?? [];
        $tipos_p = $_POST['tipo_producto'] ?? [];
        $medidas = $_POST['unidad_medida'] ?? [];
        $productos = [];
        // Asegurarse de que sean arrays
        if (!is_array($nombres)) {
            return $productos;
        }

        for ($i = 0; $i < count($nombres); $i++) {
            // Validar que todos los campos existan
            if (
                !empty($nombres[$i]) &&
                isset($un_deseadas[$i]) &&
                isset($tipos_p[$i]) &&
                isset($medidas[$i])
            ) {

                $productos[] = [
                    'id_producto' => $ids_producto[$i],
                    'nombre_producto' => trim($nombres[$i]),
                    'unidad_medida' => $medidas[$i],
                    'cantidad' => intval($un_deseadas[$i]),
                    'tipo_producto' => $tipos_p[$i]
                ];
            }
        }
        return $productos;
    }
    public function eliminarSolic()
    {
        try {
            if (!isset($_GET['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID del producto no encontrado, ha surguido un error'
                ]);
            }

            $id = $_GET['id'];
            if ($this->solicitudes->eliminarSolicitud($id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Producto eliminado exitosamente.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar'
                ]);

            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor'
            ]);
        }
        echo json_encode([
            'success' => true,
            'message' => 'Solicitud Eliminada exitosamente.'
        ]);
        exit();
    }
    public function obtenerDetalles()
    {
        header('Content-Type: application/json');

        try {
            // Verificar si es una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido'
                ]);
                return;
            }

            // Obtener ID de la solicitud
            $idSolicitud = (int) $_POST['id_solicitud'] ?? null;
            $solicitud = isset($_POST['solicitud_seleccionada'])
                ? json_decode($_POST['solicitud_seleccionada'], true)
                : null;

            if (!$idSolicitud || !$solicitud) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de solicitud no proporcionado'
                ]);
                return;
            }
            $productos_solic = $this->solicitudes->getProdsPorIdSolic($idSolicitud);

            $solicitud['productos'] = $productos_solic['data'];

            echo json_encode([
                'success' => true,
                'message' => 'Detalles obtenidos correctamente',
                'data' => $solicitud
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalles: ' . $e->getMessage()
            ]);
        }
    }
    // En tu controlador PresupuestoController.php

    public function aprobarSolicitud()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['id_solicitud']) || !isset($data['asignaciones'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $id_solicitud = $data['id_solicitud'];
        $asignaciones = $data['asignaciones'];
        $comentarios = '';
        $productos_descartados = $data['productos_descartados'] ?? [];

        try {
            // 1. Actualizar estado de la solicitud
            $this->solicitudes->actualizarEstadoSolicitud($id_solicitud, 'Aprobado', $comentarios);

            // 2. Obtener ID del solicitante
            $idSolicitante = $this->solicitudes->obtenerIdSolicitante($id_solicitud);

            // 3. Generar notificación de aprobación
            if ($idSolicitante) {
                $this->solicitudes->generarNotificacionAceptada($idSolicitante, $id_solicitud);
            }

            // 4. Insertar registros en registro_prod
            $this->solicitudes->insertarRegistrosProductos($id_solicitud, $asignaciones);

            echo json_encode([
                'success' => true,
                'message' => 'Solicitud aprobada correctamente',
                'estado' => 'Aprobado'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function agregarSolic($edit = null)
    {
        // Sanitizar y validar datos antes de guardar
        $datosSolic = [
            'oficina' => trim($_POST['departamento'] ?? 'N/A'),
            'fecha_deseada' => trim($_POST['fecha_requerida'] ?? 'N/A'),
            'oficina_solic' => trim($_POST['departamento'] ?? 'N/A'),
            'comentarios' => trim($_POST['notas'] ?? 'N/A'),
            'id_solicitante' => (int) $_POST['id_solicitante']
        ];

        if ($edit) {
            $datosSolic['id_solicitud'] = (int) $_POST['request_id'];
        }

        $productos = $this->procesarProds();

        if (empty($datosSolic['oficina']) && empty($productos[0]['nombre'])) {
            error_log("uno de los datos esta vacio");
            return false;
        } else if ($edit) {
            if (
                $this->solicitudes->eliminarSolicitud($datosSolic['id_solicitud'])
                && $this->solicitudes->guardarSolicitud($datosSolic, $datosSolic['id_solicitud'])
                && $this->solicitudes->guardarProdsSolic($productos)
            ) {
                return true;
            }
        } else {
            // Guardar la solicitud
            if (
                $this->solicitudes->guardarSolicitud($datosSolic)
                && $this->solicitudes->guardarProdsSolic($productos)
            ) {

                // Obtener el ID de la nueva solicitud creada
                $idNuevaSolicitud = $this->solicitudes->getLastInsertId();

                // Generar notificación de revisión pendiente para presupuesto
                if ($idNuevaSolicitud) {
                    $this->solicitudes->generarNotificacionRevisionPendiente(
                        $datosSolic['id_solicitante'],
                        $idNuevaSolicitud
                    );
                }

                return true;
            }
        }

        return false;
    }
    public function obtenerDetallesMultiples(array $solicitudes)
    {
        $resultado = [];

        try {
            foreach ($solicitudes as $solicitud) {

                $idSolicitud = (int) $solicitud['id_solicitud'];
                $productos_solic = $this->solicitudes->getProdsPorIdSolic($idSolicitud);
                $solicitud['productos'] = $productos_solic['data'] ?? [];

                $resultado[] = $solicitud;
            }

            return [
                'success' => true,
                'message' => 'Detalles obtenidos correctamente',
                'data' => $resultado
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener detalles: ' . $e->getMessage()
            ];
        }
    }
    private function getProductos()
    {
        try {
            $productos = $this->solicitudes->cargarProds();
            if ($productos) {
                return $productos;
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }
    public function actualizarSolic()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            error_log("EDITANDO");
            if ($this->agregarSolic(true)) {
                if (isset($_POST['nuevo_estado'])) {
                    $this->cambiarEstado();
                    exit();
                } else {
                    echo json_encode(['success' => true, 'message' => '¡Se ha actualizado su solicitud!']);
                    exit();
                }
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
        }
        // Si no entra en el if, devuelve algo por defecto
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar']);
        exit();
    }
    public function cambiarEstado()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido'
                ]);
                return;
            }

            $idSolicitud = $_POST['id_solicitud'] ?? null;
            $nuevoEstado = $_POST['nuevo_estado'] ?? null;
            $motivo = $_POST['motivo'] ?? '';

            if (!$idSolicitud || !$nuevoEstado) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
                return;
            }

            // Obtener ID del solicitante
            $idSolicitante = $this->solicitudes->obtenerIdSolicitante($idSolicitud);
            if (!$idSolicitante) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo identificar al solicitante'
                ]);
                return;
            }

            $actualizado = $this->solicitudes->actualizarEstadoSolicitud($idSolicitud, $nuevoEstado, $motivo);

            if ($actualizado) {
                // Generar notificación según el estado
                switch ($nuevoEstado) {
                    case 'Rechazado':
                        $this->solicitudes->generarNotificacionRechazada($idSolicitante, $idSolicitud, $motivo);
                        break;

                    case 'Aprobado':
                        $this->solicitudes->generarNotificacionAceptada($idSolicitante, $idSolicitud);
                        break;

                    case 'En Revisión':
                        // Cuando una solicitud pasa a revisión, notificar a presupuesto
                        $this->solicitudes->generarNotificacionRevisionPendiente($idSolicitante, $idSolicitud);
                        break;
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Solicitud colocada ' . strtolower($nuevoEstado) . ' correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo actualizar el estado de la solicitud'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}