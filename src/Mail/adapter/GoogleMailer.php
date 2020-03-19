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

    public function send(array $to, $content) {


//Create a new PHPMailer instance
        $mail = new PHPMailer;

//Tell PHPMailer to use SMTP
        $mail->isSMTP();

//Enable SMTP debugging
// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

//Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;

//Set the encryption mechanism to use - STARTTLS or SMTPS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

//Whether to use SMTP authentication
        $mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = 'antonino.visalli@gmail.com';

//Password to use for SMTP authentication
        $mail->Password = 'xhveatyfvxscmrco';

//Set who the message is to be sent from
        $mail->setFrom('from@example.com', 'First Last');

//Set an alternative reply-to address
        $mail->addReplyTo('replyto@example.com', 'First Last');

//Set who the message is to be sent to
        $mail->addAddress('visa_1984@libero.it', 'John Doe');

//Set the subject line
        $mail->Subject = 'Reset password';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
        $mail->msgHTML('test');

//Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';



//send the message, check for errors
        if (!$mail->send()) {
            echo 'Mailer Error: '. $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }

        die();
    }


}