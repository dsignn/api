<?php
declare(strict_types=1);

namespace App\Mail\adapter;

use App\Mail\ContactInterface;
use App\Mail\MailerInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;


/**
 * Class SendinblueMailer
 * @package App\Mail\adapter
 */
class SendinblueMailer  implements MailerInterface {

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

        if (isset($config['sendinblueApiKey'])) {
            $this->apiKey = $config['sendinblueApiKey'];
        }

        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function send(array $to, ContactInterface $from, string $subject, $content)
    {
        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            Configuration::getDefaultConfiguration()->setApiKey("api-key", $this->apiKey)
        );

        $sendSmtpEmail = new SendSmtpEmail();
        // FROM
        $sendSmtpEmail->setSender(new SendSmtpEmailSender(
            [
                'email' => $from->getEmail(),
                'name' =>  $from->getName()
            ]
        ));
        $sendSmtpEmail->setSubject($subject);
        // TO
        $sendSmtpEmailToArray = [];
        foreach ($to as $destination) {
            array_push($sendSmtpEmailToArray,
                new SendSmtpEmailTo(['email' => $destination->getEmail(),  'name' =>  $destination->getName()])
            );
        }
        $sendSmtpEmail->setTo($sendSmtpEmailToArray);
        // CONTENT
        $sendSmtpEmail->setHtmlContent($content);
        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            throw new \Exception('test');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}