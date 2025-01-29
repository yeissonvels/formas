<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerController extends MailerModel {
    private $subject;
    private $message;
    private $result = '';
    private $logo = '';
    private $config;

    function __construct($subject, $message, $logo = 'logo-formas-naranja.png')
    {
        parent::__construct();
        $this->subject = $subject;
        $this->message = $message;
        $this->logo = $logo;
        $this->config = $this->getMailerConfig(1);
    }

    function sendEmail() {
        // El servicio se encuentra inactivo
        if((int)$this->config->status === 0) {
            return false;
        }
        $mail = new PHPMailer(true);
        $to = $this->config->_to;
        $cc = $this->config->_cc;
        
        try {
            $mail->isSMTP();
            $mail->Host = $this->config->host; // Cambia al servidor SMTP de tu dominio
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->user;
            $mail->Password = $this->config->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            $mail->setFrom($this->config->user, $this->config->fromName);
            $mail->addAddress($to);
            $mail->addCC($cc);

            // Adjuntar imagen como embebida
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