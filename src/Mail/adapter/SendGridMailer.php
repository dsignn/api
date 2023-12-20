<?php
declare(strict_types=1);

namespace App\Mail\adapter;

use App\Mail\ContactInterface;
use App\Mail\MailerInterface;
use Psr\Log\LoggerInterface;
use SendGrid\Mail\Mail;

class SendGridMailer  implements MailerInterface {

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SendinblueMailer constructor.
     * @param array $config
     * @param LoggerInterface $logger
     */
    public function __construct(array $config, LoggerInterface $logger) {

        if (isset($config['sendGrid'])) {
            $this->apiKey = $config['sendGrid'];
        }

        $this->logger = $logger;
    }


    /**
     * @inheritDoc
     */
    public function send(array $to, ContactInterface $from, string $subject, $content) {
        // TODO: Implement send() method.

        $email = new Mail();
        $email->setFrom($from->getEmail(),  $from->getName());
        $email->setSubject($subject);

        foreach ($to as $destination) {
            $email->addTo($destination->getEmail(), $destination->getName());
        }

        $email->addContent("text/html", $content);
        $sendgrid = new \SendGrid(getenv($this->apiKey));

        try {
            $response = $sendgrid->send($email);

        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}