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
    //Autenticación de usuario
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
            if (empty($credencial)) {
                echo "<script>alert('Colocar su cedula, correo o usuario es obligatorio');</script>";
                header("Location: ?action=inicio&method=forgotPassw&error=1");
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
                $this->sendPasswRequest($correo, $id);
            } else {
                header("Location: ?action=inicio&method=forgotPassw&error=2");
                exit();
            }

            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Error chequeando si existe tu cuenta: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al chequear tu solicitud. Por favor, inténtelo de nuevo más tarde.');</script>";
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
                exit();

            } else 
            {
                header("Location: ?action=inicio&method=forgotPassw&error=2");
                exit();
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error en la recuperacion de contraseña: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al recuperar la contraseña. Por favor, inténtelo de nuevo más tarde.');</script>";
        }
    }
    private function generarCodigoRecuperacion($longitud, $id)      
    {
        $codigo = bin2hex(random_bytes($longitud / 2));
        $this->subirCodigoRecuperacion($id, $codigo);
        return $codigo;
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

            if ($fila = $result->fetch_array()) {
                header("Location: ?action=inicio&method=changePassw");
                exit();
            } else {
                header("Location: ?action=inicio&method=confirmCode&error=1");
                exit();
            }
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Error en loginAuthenticate: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.');</script>";
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
        $mail->Subject = 'Recuperacion de contraseña';
        $mail->Body = 'Coloca este codigo en la pagina para restablecer tu contrasenia: ' . $codigo;

        $mail->send();
    }
    public function changePassw($password1, $password2, $codigo)
    {
        echo 'lol';
        /*try {
            // Validar parámetros
            if (empty($codigo)) {
                echo "<script>alert('Usuario y contraseña son obligatorios');</script>";
                return false;
            }

            // Consulta preparada para prevenir inyección SQL
            $consulta = "UPDATE clave FROM inf_usuarios WHERE codigo = ?";
            $stmt = $this->db->prepare($consulta);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . ($this->db->error));
            }

            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($fila = $result->fetch_array()) {
                header("Location: ?action=inicio&method=changePassw");
                exit();
            } else {
                header("Location: ?action=inicio&method=confirmCode&error=1");
                exit();
            }
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Error en loginAuthenticate: " . $e->getMessage());
            echo "<script>alert('Ocurrió un error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.');</script>";
        }*/
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