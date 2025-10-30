<?php

namespace Classes;

// Importamos las clases necesarias de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP; // Necesario para la depuración

class Email {

    public $email;
    public $nombre;
    public $token;
    
    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    /**
     * Envía el correo de confirmación de cuenta.
     */
    public function enviarConfirmacion() {

         $mail = new PHPMailer(true);

         try {
            // Configuración del Servidor
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];

            // Configuración de Seguridad
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O SMTPS (si usas puerto 465)
            $mail->Port = $_ENV['EMAIL_PORT']; 

            // Destinatarios
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Confirma tu Cuenta';

            // Contenido del Correo
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
            
            // --- INICIO DE LA CORRECCIÓN ---
            // Usamos la URL completa para que funcione en los clientes de correo
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
            // --- FIN DE LA CORRECCIÓN ---

            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;

            // Enviar el correo
            $mail->send();
            return true;

         } catch (Exception $e) {
            echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
            return false;
         }
    }

    /**
     * Envía las instrucciones para reestablecer la contraseña.
     */
    public function enviarInstrucciones() {

        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O SMTPS (si usas puerto 465)
            $mail->Port = $_ENV['EMAIL_PORT']; 

            // Destinatarios
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Reestablece tu password';

            // Contenido
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
            
            // --- INICIO DE LA CORRECCIÓN ---
            // Usamos la URL completa para que funcione en los clientes de correo
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/recuperar?token=" . $this->token . "'>Reestablecer Password</a>";
            // --- FIN DE LA CORRECCIÓN ---
            
            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;

            //Enviar el mail
            $mail->send();
            return true;

        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }
}