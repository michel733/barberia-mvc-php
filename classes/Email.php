<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

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

    public function enviarConfirmacion() {

         // create a new object and configure SMTP (use env vars with Brevo defaults)
         $mail = new PHPMailer(true);
         try {
             $mail->isSMTP();
             
             // Verificar que todas las variables de entorno necesarias estén configuradas
             $host = getenv('EMAIL_HOST');
             $port = getenv('EMAIL_PORT');
             $username = getenv('EMAIL_USER');
             $password = getenv('EMAIL_PASSWORD');
             $from = getenv('EMAIL_FROM');

             if (!$host || !$port || !$username || !$password || !$from) {
                 error_log('[CONFIG DEBUG] Host: ' . ($host ? 'OK' : 'FALTA'));
                 error_log('[CONFIG DEBUG] Port: ' . ($port ? 'OK' : 'FALTA'));
                 error_log('[CONFIG DEBUG] Username: ' . ($username ? 'OK' : 'FALTA'));
                 error_log('[CONFIG DEBUG] Password: ' . ($password ? 'OK' : 'FALTA'));
                 error_log('[CONFIG DEBUG] From: ' . ($from ? 'OK' : 'FALTA'));
                 throw new PHPMailerException('Faltan configuraciones SMTP requeridas. Por favor, configure todas las variables de entorno necesarias.');
             }

             // Habilitar debug de SMTP
             $mail->SMTPDebug = 2;
             error_log('[CONFIG INFO] Intentando conexión SMTP con: ' . $host . ':' . $port);
             
             $mail->Host = $host;
             $mail->SMTPAuth = true;
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port = (int)$port;
             $mail->Username = $username;
             $mail->Password = $password;

             $from = getenv('EMAIL_FROM');
             $fromName = getenv('EMAIL_FROM_NAME') ?: 'AppSalon';
             $mail->setFrom($from, $fromName);
             $mail->addAddress($this->email, $this->nombre);
             $mail->Subject = 'Confirma tu Cuenta';
         } catch (PHPMailerException $e) {
             error_log('[MAIL CONFIG ERROR] ' . $e->getMessage());
         }

         // Set HTML
         $mail->isHTML(TRUE);
         $mail->CharSet = 'UTF-8';

         $contenido = '<html>';
         $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
         $contenido .= "<p>Presiona aquí: <a href='" . getenv('PROJECT_URL') . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
         $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
         $contenido .= '</html>';
         $mail->Body = $contenido;

         //Enviar el mail
         try {
             error_log('[MAIL INFO] Intentando enviar correo a: ' . $this->email);
             if ($mail->send()) {
                 error_log('[MAIL SUCCESS] Correo enviado exitosamente a: ' . $this->email);
                 return true;
             } else {
                 error_log('[MAIL ERROR] Error al enviar: ' . $mail->ErrorInfo);
                 return false;
             }
         } catch (PHPMailerException $e) {
             error_log('[MAIL ERROR] Exception: ' . $e->getMessage());
             return false;
         }

    }

    public function enviarInstrucciones() {

          // create a new object and configure SMTP (use env vars with Brevo defaults)
            $mail = new PHPMailer(true);
            try {
                 $mail->isSMTP();
                 
                 // Verificar que todas las variables de entorno necesarias estén configuradas
                 $host = getenv('EMAIL_HOST');
                 $port = getenv('EMAIL_PORT');
                 $username = getenv('EMAIL_USER');
                 $password = getenv('EMAIL_PASSWORD');
                 $from = getenv('EMAIL_FROM');

                 if (!$host || !$port || !$username || !$password || !$from) {
                     throw new PHPMailerException('Faltan configuraciones SMTP requeridas. Por favor, configure todas las variables de entorno necesarias.');
                 }

                 $mail->Host = $host;
                 $mail->SMTPAuth = true;
                 $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                 $mail->Port = (int)$port;
                 $mail->Username = $username;
                 $mail->Password = $password;

                 $from = getenv('EMAIL_FROM');
                $fromName = getenv('EMAIL_FROM_NAME') ?: 'AppSalon';
                $mail->setFrom($from, $fromName);
                $mail->addAddress($this->email, $this->nombre);
                $mail->Subject = 'Reestablece tu password';
            } catch (PHPMailerException $e) {
                error_log('[MAIL CONFIG ERROR] ' . $e->getMessage());
            }

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . getenv('PROJECT_URL') . "/recuperar?token=" . $this->token . "'>Reestablecer Password</a>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

            //Enviar el mail
            try {
                $mail->send();
            } catch (PHPMailerException $e) {
                error_log('[MAIL ERROR] ' . $e->getMessage());
            }
    }
}