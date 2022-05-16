<?php
namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class MailerController {

    private $mail_mailer;
    private $mail_host;
    private $mail_port;
    private $mail_username;
    private $mail_password;
    private $mail_encryption;
    private $mail_from_address;
    private $mail_from_name;
    
    public  $mail_body;
    public  $mail;


    function __construct($env)
    {
        $this->mail_mailer = $env['MAIL_MAILER'];
        $this->mail_host = $env['MAIL_HOST'];
        $this->mail_port = $env['MAIL_PORT'];
        $this->mail_username = $env['MAIL_USERNAME'];
        $this->mail_password = $env['MAIL_PASSWORD'];
        $this->mail_encryption = $env['MAIL_ENCRYPTION'];    
        $this->mail_from_address = $env['MAIL_FROM_ADDRESS'];
        $this->mail_from_name = $env['MAIL_FROM_NAME'];
        
        //Instantiation and passing `true` enables exceptions
        $this->mail = new PHPMailer(true);

        //Server settings
        $this->mail->SMTPDebug  = $env['APP_ENV'] === 'development' ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $this->mail->Mailer     = $this->mail_mailer;                      
        $this->mail->Host       = $this->mail_host;
        $this->mail->Port       = $this->mail_port;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $this->mail_username;
        $this->mail->Password   = $this->mail_password;
        $this->mail->SMTPSecure = $this->mail_encryption;
        $this->mail->Port       = 587;
        $this->mail->CharSet    = 'UTF-8';

        //Recipients
        $this->mail->setFrom($this->mail_from_address, $this->mail_from_name);
        $this->mail->addReplyTo($this->mail_from_address, $this->mail_from_name);

    }

    public function setBody($html_body)
    {      

        $alt_body = strip_tags( // html tagを排除する
            str_replace( // <br/>を \n に変換
                '<br/>', 
                '\n', 
                nl2br($html_body) //改行を<br/>に変換
            ) 
        );

        $this->mail->Body    = nl2br($html_body);
        $this->mail->AltBody = $alt_body;  
        
        return $this;
    }

    public function setSubject($title) 
    {
        $this->mail->Subject = $title;

        return $this;
    }

    public function setAddress($address) 
    {
        if(is_array($address)) {

            foreach ($address as $value) {
                $this->mail->addAddress($value);
            }

            return $this;

        } else if(is_string($address)) {

            $this->mail->addAddress($address);

            return $this;

        } else {

            throw new \Exception('address param need tobe array or string');

        }
    }

    public function sendMail()
    {
        try {
            
            $this->mail->send();

            return true;

        } catch (\Exception $e) {

            throw new \Exception($this->mail->ErrorInfo);

        }
    }
    
    public function clearAddress() 
    {
        $this->mail->clearAddresses();

        return $this;
    }

    public static function getMailTemplate() 
    {   

        $file_name = __DIR__.'/../template.txt';
        return self::getTemplateFile($file_name);

    }

    public static function getMailNotificaitonTemplate() 
    {   

       $file_name = __DIR__.'/../notification-template.txt';
       return self::getTemplateFile($file_name);

    }

    private static function getTemplateFile($file_name) 
    {   
        if(!isset($file_name) || empty($file_name)) throw new Exception("file name is not exists or empty.", 1);
        
        $file_exists = file_exists ($file_name);

        if(!$file_exists) throw new \Exception('cannot find template.txt in root direcotry');

        $file = file_get_contents($file_name);

        return $file; 
    }

    public function generateMailTemplate($template, $form_data) 
    {
        $new_str = $template;

        foreach ($form_data as $key => $value) {
            $new_str = str_replace('{{'. $key .'}}', $value, $new_str);
        }

        return $new_str;

    }



}