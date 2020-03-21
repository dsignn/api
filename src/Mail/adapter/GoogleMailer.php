<?php
declare(strict_types=1);

namespace App\Mail\adapter;


use App\Mail\MailerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Class GoogleMailer
 * @package App\Mail\adapter
 */
class GoogleMailer implements MailerInterface {

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $port = 587;

    /**
     * @var string
     */
    protected $host = 'smtp.gmail.com';

    /**
     * GoogleMailer constructor.
     * @param $config
     */
    public function __construct(array $config) {

        if (isset($config['password'])) {
            $this->password = $config['password'];
        }

        if (isset($config['username'])) {
            $this->username = $config['username'];
        }
    }

    /**
     * @inheritDoc
     */
    public function send(array $to, $content) {

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom('from@example.com', 'First Last');
        $mail->addReplyTo('replyto@example.com', 'First Last');
        foreach ($to as $destination) {
            $mail->addAddress($destination, );
        }
        $mail->Subject = 'Reset password';
        $mail->msgHTML($content);
        $mail->AltBody = 'This is a plain-text message body';

        if (!$mail->send()) {
            // TODO
        }
    }


}