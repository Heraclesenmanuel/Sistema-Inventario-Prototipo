<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';


class Inicio 
{
    private $db; 
    private $modeloDB;
    
    public function __construct() 
    {
        $this->modeloDB = new BaseDatos();
        $this->db = $this->modeloDB->conectar();
        $this->iniciarSesion();
    }

    private function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function loginAuthenticate($usuario, $password)
    {
        try {
            // Validar parámetros
            if (empty($usuario) || empty($password)) {
                echo "<script>alert('Usuario y contraseña son obligatorios');</script>";
                return false;
            }

            // Consulta preparada para prevenir inyección SQL
            $consulta = "SELECT * FROM inf_usuarios WHERE cedula = ? AND clave = ?";
            $stmt = $this->db->prepare($consulta);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }

            $stmt->bind_param("ss", $usuario, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($fila = $result->fetch_array()) {
                $this->establecerSesionUsuario($fila);
                $this->redirigirSegunRol($fila['id_cargo']);
            } else {
                header("Location: ?action=inicio&method=login&error=1");
                exit();
            }
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Error en loginAuthenticate: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }
    public function checkPasswRequest($credencial)
    {
        try {
            // Validar parámetros
            if (strlen(trim($credencial))==0) {
                echo "<script>
                    alert('Debe ingresar su cedula, correo o nombre de usuario para solicitar el código de verificación.');
                    window.location.href = '?action=inicio&method=forgotPassw&error=1';
                </script>";
                exit();
            }

            // Consulta preparada para prevenir inyección SQL
            $consulta = "SELECT correo, id FROM inf_usuarios WHERE cedula = ? OR correo = ?";
            $stmt = $this->db->prepare($consulta);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }

            $stmt->bind_param("ss", $credencial, $credencial);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($fila = $result->fetch_array()) {
                $correo = $fila[0];
                $id = $fila[1];
                $_SESSION['id'] = $id;
                $this->sendPasswRequest($correo, $id);
                $stmt->close();
            } 
            else if($result->num_rows == 0)
            {
                header("Location: ?action=inicio&method=forgotPassw&error=1");
                $stmt->close();
                exit();
            }
            else {
                header("Location: ?action=inicio&method=forgotPassw&error=2");
                $stmt->close();
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Error chequeando si existe tu cuenta: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al chequear tu solicitud. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }
        private function sendPasswRequest($correo, $id)
    {
        $codigo = $this->generarCodigoRecuperacion(32, $id);
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '5b70b234403b01';
        $mail->Password = '87f37005a895f3';
        $mail->Port = 2525;

        $mail->setFrom('noreply@demo.com', 'Sistema UPEL');
        $mail->addAddress(strtolower($correo));
        $mail->Subject = 'Recuperacion de contrasenia';
        $mail->Body = 'Coloca este codigo en la pagina para restablecer tu contrasenia: ' . $codigo;

        $mail->send();
    }
        private function generarCodigoRecuperacion($longitud, $id)      
    {
        $codigo = bin2hex(random_bytes($longitud / 2));
        $this->verificarCodigoAnterior($id, $codigo);
        return $codigo;
    }
    private function verificarCodigoAnterior($id, $codigo)
    {
        try
        {
            $consulta = "SELECT * FROM codigos_recuperacion WHERE id=?";
            $stmt = $this->db->prepare($consulta);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }
            $stmt->bind_param("i", $id);
            $ejecucion = $stmt->execute();
            $stmt->store_result();
            if (!$ejecucion) 
            {
                header("Location: ?action=inicio&method=forgotPassw&error=2");
                $stmt->close();
                exit();
            }
            else if ($stmt->num_rows > 0)
            {
                header("Location: ?action=inicio&method=confirmCode&msg=1");
                $stmt->close();
                exit();
            }
            else
            {
                $this->subirCodigoRecuperacion($id, $codigo);
            }
            exit($stmt->num_rows);
        } catch (Exception $e) {
            error_log("Error en la recuperacion de contraseña: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al recuperar la contraseña. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }
    private function subirCodigoRecuperacion($id, $codigo)
    {
        try
        {
            $consulta = "INSERT INTO codigos_recuperacion VALUES(?, ?)";
            $stmt = $this->db->prepare($consulta);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }
            $stmt->bind_param("is", $id, $codigo);
            if ($stmt->execute()) 
            {
                header("Location: ?action=inicio&method=confirmCode");
                $stmt->close();
            } else 
            {
                header("Location: ?action=inicio&method=forgotPassw&error=2");
                $stmt->close();
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Error en la recuperacion de contraseña: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al recuperar la contraseña. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }
    public function chequearCodigo($codigo)
    {
        try {
            // Validar parámetros
            if (empty($codigo)) {
                echo "<script>alert('Usuario y contraseña son obligatorios');</script>";
                return false;
            }

            // Consulta preparada para prevenir inyección SQL
            $consulta = "SELECT * FROM codigos_recuperacion WHERE codigo = ?";
            $stmt = $this->db->prepare($consulta);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }

            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->fetch_array()) {
                header("Location: ?action=inicio&method=changePassw");             
            } else if ($result->num_rows == 0){
                header("Location: ?action=inicio&method=confirmCode&error=1");
            }
            else{
                header("Location: ?action=inicio&method=confirmCode&error=2");                
            }
            $stmt->close();
            exit();
        } catch (Exception $e) {
            error_log("Error en chequeo de codigo: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al chequear el codigo. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }
    public function changePassw($password1, $password2, $codigo)
    {
        try {
            // Validar parámetros
            if (empty($codigo) || $password1!==$password2) {
                echo "<script>alert('La contraseña y su confirmación son obligatorias y deben ser iguales.');</script>";
                return false;
            }

            // Consulta preparada para prevenir inyección SQL
            $consulta = "UPDATE inf_usuarios set clave=? WHERE id=?";
            $stmt = $this->db->prepare($consulta);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }

            $stmt->bind_param("ss", $password1, $codigo);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: ?action=inicio&method=login&state=1");
            } else {
                header("Location: ?action=inicio&method=changePassw&error=1");
            }
            $stmt->close();
            exit();
        } catch (Exception $e) {
            error_log("Error en loginAuthenticate: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }

    private function establecerSesionUsuario($datosUsuario)
    {
        $_SESSION['nombre'] = $datosUsuario['nombre'];
    }

    public function obtenerNombreUsuario($nombre)
    {
        $consulta = "SELECT nombre FROM inf_usuarios WHERE nombre = ?";
        $stmt = $this->db->prepare($consulta);

        if (!$stmt) {
            error_log("Error preparando consulta: " . ($this->db->error));
            return null;
        }

        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $stmt->close();

        return $usuario['nombre'] ?? null;
    }
    private function redirigirSegunRol($idCargo)
    {
        switch ($idCargo) {
            case 1:
                header("Location: ?action=admin");
                break;
            default:
                echo "<script>alert('Rol de usuario no válido');</script>";
                return;
        }
        exit();
    }
}