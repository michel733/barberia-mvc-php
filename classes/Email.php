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
             $mail->Host = getenv('EMAIL_HOST') ?: 'smtp-relay.brevo.com';
             $mail->SMTPAuth = true;
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port = (int)(getenv('EMAIL_PORT') ?: 587);
             $mail->Username = getenv('EMAIL_USER') ?: '980e91002@smtp-brevo.com';
             $mail->Password = getenv('EMAIL_PASSWORD') ?: 'TxkXE9hn4UdbPlVD';

             $from = getenv('EMAIL_FROM') ?: 'no-reply@cultured-phase-qif.domcloud.dev';
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
         $contenido .= "<p><strong>Hola " . $this->email .  "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
         $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
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

    public function enviarInstrucciones() {

          // create a new object and configure SMTP (use env vars with Brevo defaults)
            $mail = new PHPMailer(true);
            try {
                 $mail->isSMTP();
                 $mail->Host = getenv('EMAIL_HOST') ?: 'smtp-relay.brevo.com';
                 $mail->SMTPAuth = true;
                 $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                 $mail->Port = (int)(getenv('EMAIL_PORT') ?: 587);
                 $mail->Username = getenv('EMAIL_USER') ?: '980e91002@smtp-brevo.com';
                 $mail->Password = getenv('EMAIL_PASSWORD') ?: 'TxkXE9hn4UdbPlVD';

                $from = getenv('EMAIL_FROM') ?: 'no-reply@cultured-phase-qif.domcloud.dev';
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
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/recuperar?token=" . $this->token . "'>Reestablecer Password</a>";
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