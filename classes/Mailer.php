<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    function __construct($to, $subject, $msg, $attachments = array()) {
        $mailer = new PHPMailer(true);

        try {
            $mailer->SMTPDebug = 2;
            $mailer->isSMTP();
            $mailer->Host = 'mx1.hostinger.es';  // Specify main and backup SMTP servers
            $mailer->SMTPAuth = true;                               // Enable SMTP authentication
            $mailer->Username = 'info@velezdesarrollo.com';                 // SMTP username
            $mailer->Password = 'vdf_survey_R200';                           // SMTP password
            $mailer->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mailer->Port = 587;

            $mailer->setFrom("info@velezdesarrollo.com");
            $mailer->AddAddress($to);
            $mailer->addReplyTo("info@velezdesarrollo.com");

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            foreach ($attachments as $attachment) {
                $mailer->addAttachment($attachment);
            }

            //Content
            $mailer->isHTML(true);                                  // Set email format to HTML
            $mailer->Subject = $subject;
            $mailer->Body    = $msg;
            $mailer->AltBody =  strip_tags($msg); // Texto plano si no acepta HTML

            $mailer->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mailer->ErrorInfo;
        }

        echo "Aqui";
        exit;
    }
}