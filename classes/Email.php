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
    public $fecha;
    public $hora;
    public $servicios;
    
    public function __construct($email, $nombre, $token = '', $fecha = '', $hora = '', $servicios = [])
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->servicios = $servicios;
    }

    /**
     * Envía el correo de confirmación de cuenta.
     */
    public function enviarConfirmacion() {

         $mail = new PHPMailer(true);

         try {
            // ... (Configuración del servidor, puertos, etc. - todo eso está bien) ...
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = $_ENV['EMAIL_PORT']; 

            // Destinatarios
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Confirma tu Cuenta';

            // Contenido del Correo
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            // --- INICIO DE LA MEJORA ---
            $contenido .= '<body>';
            // --- FIN DE LA MEJORA ---
            
            $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";

            // --- INICIO DE LA MEJORA ---
            $contenido .= '</body>';
            // --- FIN DE LA MEJORA ---
            $contenido .= '</html>';
            
            $mail->Body = $contenido;

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
            // ... (Configuración del servidor, puertos, etc. - todo eso está bien) ...
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT']; 

            // Destinatarios
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Reestablece tu password';

            // Contenido
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            // --- INICIO DE LA MEJORA ---
            $contenido .= '<body>';
            // --- FIN DE LA MEJORA ---

            // ¡ASEGÚRATE DE QUE ESTA LÍNEA TENGA <p> Y NO p> EN TU SERVIDOR!
            $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/recuperar?token=" . $this->token . "'>Reestablecer Password</a>";
            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";

            // --- INICIO DE LA MEJORA ---
            $contenido .= '</body>';
            // --- FIN DE LA MEJORA ---
            $contenido .= '</html>';
            
            $mail->Body = $contenido;

            $mail->send();
            return true;

        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    /**
     * Envía un recordatorio de cita 15 minutos antes.
     */
    public function enviarRecordatorio() {

        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT']; 

            // Destinatarios
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Recordatorio de tu Cita - Barberia Nuevo Estilo';

            // Contenido
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            // Formatear la fecha en español
            $fechaObj = new \DateTime($this->fecha);
            $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            $diaSemana = $dias[$fechaObj->format('w')];
            $dia = $fechaObj->format('d');
            $mes = $meses[(int)$fechaObj->format('m')];
            $anio = $fechaObj->format('Y');
            
            $fechaFormateada = "$diaSemana, $dia de $mes de $anio";

            // Crear lista de servicios
            $listaServicios = '';
            $total = 0;
            foreach($this->servicios as $servicio) {
                $listaServicios .= "<li style='margin: 5px 0;'>{$servicio['nombre']} - \${$servicio['precio']}</li>";
                $total += $servicio['precio'];
            }

            $contenido = '<html>';
            $contenido .= '<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
            $contenido .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">';
            
            // Header
            $contenido .= '<div style="background-color: #0da6f3; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">';
            $contenido .= '<h1 style="margin: 0;">Barberia Nuevo Estilo</h1>';
            $contenido .= '</div>';
            
            // Body
            $contenido .= '<div style="background-color: white; padding: 30px; border-radius: 0 0 5px 5px;">';
            $contenido .= "<h2 style='color: #0da6f3;'>¡Recordatorio de tu Cita!</h2>";
            $contenido .= "<p><strong>Hola {$this->nombre},</strong></p>";
            $contenido .= "<p>Te recordamos que tienes una cita programada en <strong>15 minutos</strong>:</p>";
            
            // Información de la cita
            $contenido .= '<div style="background-color: #f9f9f9; padding: 20px; border-left: 4px solid #0da6f3; margin: 20px 0;">';
            $contenido .= "<p style='margin: 5px 0;'><strong>📅 Fecha:</strong> {$fechaFormateada}</p>";
            $contenido .= "<p style='margin: 5px 0;'><strong>🕐 Hora:</strong> {$this->hora}</p>";
            $contenido .= '</div>';
            
            // Servicios
            $contenido .= '<h3 style="color: #0da6f3;">Servicios:</h3>';
            $contenido .= "<ul style='list-style: none; padding: 0;'>{$listaServicios}</ul>";
            $contenido .= "<p style='font-size: 18px; font-weight: bold; color: #0da6f3;'>Total: \$" . number_format($total, 2) . "</p>";
            
            // Footer
            $contenido .= '<hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">';
            $contenido .= '<p style="font-size: 14px; color: #666;">Te esperamos en Barberia Nuevo Estilo</p>';
            $contenido .= '<p style="font-size: 12px; color: #999;">Si necesitas cancelar o reprogramar, por favor contáctanos lo antes posible.</p>';
            $contenido .= '</div>';
            
            $contenido .= '</div>';
            $contenido .= '</body>';
            $contenido .= '</html>';
            
            $mail->Body = $contenido;

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error al enviar recordatorio: {$mail->ErrorInfo}");
            return false;
        }
    }

}
