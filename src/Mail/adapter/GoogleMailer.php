<?php
declare(strict_types=1);

namespace App\Mail\adapter;


use App\Mail\ContactInterface;
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
    public function send(array $to, ContactInterface $from, string $subject, $content) {

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
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

/*
 *
 2021-01-0521: 40: 29SERVER-&gt;CLIENT: 220smtp.gmail.comESMTPq37sm257530qte.10-gsmtp<br>2021-01-0521: 40: 29CLIENT-&gt;SERVER: EHLOapi-staging.ds-ign.it<br>2021-01-0521: 40: 29SERVER-&gt;CLIENT: 250-smtp.gmail.comatyourservice,
[
  23.235.221.220
]250-SIZE35882577250-8BITMIME250-STARTTLS250-ENHANCEDSTATUSCODES250-PIPELINING250-CHUNKING250SMTPUTF8<br>2021-01-0521: 40: 29CLIENT-&gt;SERVER: STARTTLS<br>2021-01-0521: 40: 29SERVER-&gt;CLIENT: 2202.0.0ReadytostartTLS<br>2021-01-0521: 40: 29CLIENT-&gt;SERVER: EHLOapi-staging.ds-ign.it<br>2021-01-0521: 40: 30SERVER-&gt;CLIENT: 250-smtp.gmail.comatyourservice,
[
  23.235.221.220
]250-SIZE35882577250-8BITMIME250-AUTHLOGINPLAINXOAUTH2PLAIN-CLIENTTOKENOAUTHBEARERXOAUTH250-ENHANCEDSTATUSCODES250-PIPELINING250-CHUNKING250SMTPUTF8<br>2021-01-0521: 40: 30CLIENT-&gt;SERVER: AUTHLOGIN<br>2021-01-0521: 40: 30SERVER-&gt;CLIENT: 334VXNlcm5hbWU6<br>2021-01-0521: 40: 30CLIENT-&gt;SERVER: [
  credentialshidden
]<br>2021-01-0521: 40: 30SERVER-&gt;CLIENT: 334UGFzc3dvcmQ6<br>2021-01-0521: 40: 30CLIENT-&gt;SERVER: [
  credentialshidden
]<br>2021-01-0521: 40: 30SERVER-&gt;CLIENT: 535-5.7.8UsernameandPasswordnotaccepted.Learnmoreat5355.7.8https: //support.google.com/mail/?p=BadCredentialsq37sm257530qte.10-gsmtp<br>2021-01-0521: 40: 30SMTPERROR: Passwordcommandfailed: 535-5.7.8UsernameandPasswordnotaccepted.Learnmoreat5355.7.8https: //support.google.com/mail/?p=BadCredentialsq37sm257530qte.10-gsmtp<br>SMTPError: Couldnotauthenticate.<br>2021-01-0521: 40: 30CLIENT-&gt;SERVER: QUIT<br>2021-01-0521: 40: 30SERVER-&gt;CLIENT: 2212.0.0closingconnectionq37sm257530qte.10-gsmtp<br>SMTPError: Couldnotauthenticate.<br><br/><b>Fatalerror</b>: UncaughtTypeError: fwrite()expectsparameter2tobestring,
 */