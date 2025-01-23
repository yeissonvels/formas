<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerController {
    private $to;
    private $subject;
    private $message;
    private $result = '';
    private $logo = '';

    function __construct($to, $subject, $message, $logo = 'logo-formas-naranja.png')
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->logo = $logo;
    }

    function sendEmail() {
        $mail = new PHPMailer(true);
        $to = $this->to;
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com'; // Cambia al servidor SMTP de tu dominio
            $mail->SMTPAuth = true;
            $mail->Username = 'info@riodevs.com';
            $mail->Password = 'vdf_survey_R200';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            $mail->setFrom('info@riodevs.com', 'Info');
            $mail->addAddress("$to");

            // Adjuntar imagen como embebida
            $embeddedImagen = 
            $mail->addEmbeddedImage((IMAGES_DIR . $this->logo), 'imagenCID');
        
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->message;
        
            $mail->send();
            
            return true;
        } catch (Exception $e) {
            $this->result = $mail->ErrorInfo;
            return false;
        }
    }

    function getResult() {
        return $this->result;
    }
}