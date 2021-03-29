<?php

namespace Src\Models;

use Exception;
use stdClass;
use PHPMailer\PHPMailer\PHPMailer;
use Router\Model\Model;

class Email extends Model
{
    /**@var PHPMailer */
    private $mail;

    /**@var stdClass */
    private $data;

    /**@var Exception */
    private $error;

    public function __construct()
    {
        require_once dirname(__DIR__, 1). '/Config.php';

        $this->mail = new PHPMailer(true);
        $this->data = new stdClass();

        $this->mail->isSMTP();
        $this->mail->isHTML();
        $this->mail->setLanguage("br");

        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->CharSet = "utf-8";

        $this->mail->Host = MAIL['host'];
        $this->mail->Port = MAIL['port'];
        $this->mail->Username = MAIL['user'];
        $this->mail->Password = MAIL['passwd'];
    }

    public function add($subject, $body, $recipient_name, $recipient_email)
    {
        $this->data->subject = $subject;
        $this->data->body = $body;
        $this->data->recipient_name = $recipient_name; 
        $this->data->recipient_email = $recipient_email;

        return $this;
    }

    public function attach($filePath, $fileName)
    {
        $this->data->attach[$filePath] = $fileName;
    }

    public function send($from_name = MAIL['from_name'], $from_email = MAIL['from_email'])
    {
        try {

            $this->mail->Subject = $this->data->subject;
            $this->mail->msgHTML($this->data->body);
            $this->mail->addAddress($this->data->recipient_email, $this->data->recipient_name);
            $this->mail->setFrom($from_email, $from_name);

            if(!empty($this->data->attach)) {
                foreach($this->data->attach as $path => $name) {
                    $this->mail->addAddress($path, $name);
                }
            }

            $this->mail->send();
            return true;

        } catch(Exception $exception) {
            $this->error = $exception;
            return false;
        }
    }

    public function error()
    {
        return $this->error;
    }
}