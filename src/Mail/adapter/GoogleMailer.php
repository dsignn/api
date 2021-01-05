<?php
declare(strict_types=1);

namespace App\Mail\adapter;


use App\Mail\ContactInterface;
use App\Mail\MailerInterface;
use PHPMailer\PHPMailer\PHPMailer;

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
    public function send(array $to, ContactInterface $from, string $subject, $content) {

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = $this->username;
        $mail->Password = $this->password;

        $mail->setFrom($from->getEmail(), $from->getName());
        foreach ($to as $destination) {
            /** @var $destination ContactInterface */
            $mail->addAddress($destination->getEmail(), $destination->getName());
        }
        $mail->Subject = $subject;
        $mail->msgHTML($content);

        if (!$mail->send()) {
            // TODO
        }

        return $this;
    }


}