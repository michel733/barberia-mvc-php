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
     * Devuelve true si se envió, false si hubo un error.
     */
    public function enviarConfirmacion() {

         // Crear una nueva instancia de PHPMailer; `true` habilita excepciones
         $mail = new PHPMailer(true);

         try {
            // -- PASO 1: Configuración del Servidor --

            /* * Habilita la salida de depuración detallada.
             * Descomenta esta línea si sigues teniendo problemas.
             * 0 = Sin salida
             * 1 = Mensajes del cliente
             * 2 = Mensajes del cliente y servidor (MEJOR PARA DEPURAR)
             */
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 

            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];

            /* * -- PASO 2: Configuración de Seguridad (¡IMPORTANTE!) --
             * Usa TLS o SMTPS (SSL).
             */
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Generalmente para puerto 587
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Generalmente para puerto 465

            $mail->Port = $_ENV['EMAIL_PORT']; // Puerto 587 para TLS, 465 para SMTPS (SSL)

            // -- PASO 3: Destinatarios --
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo'); // Añade un nombre al remitente
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Confirma tu Cuenta';

            // -- PASO 4: Contenido del Correo --
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            // ¡Corrección! Usar $this->nombre en el saludo
            $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;

            // -- PASO 5: Enviar el correo --
            $mail->send();
            return true; // Éxito

         } catch (Exception $e) {
            // -- PASO 6: Manejo de Errores --
            // Si algo falla, el 'catch' se activa y muestra el error.
            // En producción, deberías registrar este error en un log, no imprimirlo.
            echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
            return false; // Fracaso
         }
    }

    /**
     * Envía las instrucciones para reestablecer la contraseña.
     * Devuelve true si se envió, false si hubo un error.
     */
    public function enviarInstrucciones() {

        // Crear una nueva instancia de PHPMailer; `true` habilita excepciones
        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Descomenta para depurar
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O SMTPS
            $mail->Port = $_ENV['EMAIL_PORT']; 

            var_dump($_ENV['EMAIL_HOST']);
            var_dump($_ENV['EMAIL_USER']);

            // Destinatarios
            $mail->setFrom('cuentas@barberianuevoestilo.com', 'Barberia Nuevo Estilo');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Reestablece tu password';

            // Contenido
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = '<html>';
            $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/recuperar?token=" . $this->token . "'>Reestablecer Password</a>";
            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;

            //Enviar el mail
            $mail->send();
            return true; // Éxito

        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
            return false; // Fracaso
        }
    }
}