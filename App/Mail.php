<?php
namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail
 * 
 * Php Version 7.3.6
 */

class Mail
{
    /**
     * Envía un mensaje
     * 
     * 
     * @param string $to Destinatario
     * @param string $subject Asunto
     * @param string $text Contenido del mensaje solo en texto
     * @param string $html Contenido del mensaje HTML
     * @param bool $html_bool Html en el mensaje true
     * 
     * @return mixed
     */
    public static function send($to, $subject, $text, $html, $html_bool = true)
    {
        
    
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                       // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = CONFIG::PHP_MAILER_HOST;                    // Set the SMTP server to send through
            $mail->SMTPAuth   = false;                                   // Enable SMTP authentication
            $mail->Username   = 'user@example.com';                     // SMTP username
            $mail->Password   = 'secret';                               // SMTP password
            $mail->SMTPSecure = false; //PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = CONFIG::PHP_MAILER_PORT;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress($to);     // Add a recipient

            /*
             
            // Attachments
            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            */

            // Content
            if ($html_bool == true) {

                $mail->isHTML(true); 
                $mail->Body = $html;

            } else {
                $mail->Body = $text;
            }

            $mail->Subject = $subject;  
            $mail->AltBody = $text;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) 
        {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }




}
