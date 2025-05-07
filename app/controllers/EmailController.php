<?php
// app/controllers/EmailController.php
// Controlador para el envío de correos


require_once realpath( __DIR__.'/../../core/Load.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



require_once realpath( __DIR__ . "/../../public/vendor/PHPMailer/src/Exception.php");
require_once realpath( __DIR__ . "/../../public/vendor/PHPMailer/src/PHPMailer.php");
require_once realpath( __DIR__ . "/../../public/vendor/PHPMailer/src/SMTP.php");

class EmailController {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(TRUE);
    //  $this->mailer->SMTPDebug = 2;
        $this->mailer->CharSet = 'UTF-8'; 
        $this->mailer->Encoding = 'base64';
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;

    // Datos del servidor y usuario
        $this->mailer->Host = M_Host;
        $this->mailer->Port = M_Port;
        $this->mailer->Username = M_Username;
        $this->mailer->Password = M_Password;
        $this->mailer->SMTPSecure = M_Secure;
        $this->mailer->Timeout = 60; 

    // Remitente
        $this->mailer->setFrom(M_From, M_Name);
        $this->mailer->isHTML(true);
    //  $this->mailer->AltBody = 'El texto como elemento de texto simple';
    }

    public function sendMail($address, $subject,$body,$email=M_From ) {
        
       try {
        $this->mailer->addReplyTo($email, 'Responder a '.$email );
        $this->mailer->addAddress($address, '');
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        $this->mailer->send(); 
 
        return 'okMailSend';
    } catch (Exception $e) {
        if (DebugMode) {
 
            return  $e->getMessage(); //'noMailSend';
        }
        else { 
             return  'noMailSend';
        }
        //echo '2Error al enviar el correo: ' . $e->getMessage();
    }

}


public function sendAsyncMail($params){

$htmlContent = file_get_contents(realpath( ABSPATH . $params['htmlContent']));
$address =isset($params['address'])?$params['address']:M_From;;
$email= isset($params['email'])?$params['email']:M_From;
$subject = $params['title']??''; 
$body = str_replace('{{title}}', $subject, $htmlContent); 
/*$aText=$params['aText']??'';
$aHref = $params['aHref']??'';
$name = $params['name']??'';
$h1= $params['h1']??'';
$p1=$params['p1']??'';
$p2=$params['p2']??'';
$logo=$params['logo']??''; */


foreach ($params as $key => $value) {
        if ($key === 'message') {
          $lines = preg_split("/\r\n/", $value);
          $value = "<p>" . implode("</p><p>", $lines) . "</p>";
        }
       
        $body = str_replace('{{' . $key . '}}', $value, $body);
}

/*$body = str_replace('{{title}}', $subject, $htmlContent);
$body = str_replace('{{aText}}', $aText, $body);
$body = str_replace('{{aHref}}', $aHref, $body);
$body = str_replace('{{h1}}', $h1, $body);
$body = str_replace('{{p1}}', $p1, $body);
$body = str_replace('{{p2}}', $p2, $body);
$body = str_replace('{{logo}}', $logo, $body);*/
        
       try {
        $this->mailer->addReplyTo($email, 'Responder a '.$email );
        $this->mailer->addAddress($address, '');
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        $this->mailer->send(); 
 
        return 'okMailSend';
    } catch (Exception $e) {
        if (DebugMode) {
 
            return  $e->getMessage(); //'noMailSend';
        }
        else { 
             return  'noMailSend';
        }
        //echo '2Error al enviar el correo: ' . $e->getMessage();
    }

}


}

