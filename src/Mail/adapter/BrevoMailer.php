<?php
declare(strict_types=1);

namespace App\Mail\adapter;

use App\Mail\ContactInterface;
use App\Mail\MailerInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;

/**
 * Class BrevoMailer
 * @package App\Mail\adapter
 */
class BrevoMailer  implements MailerInterface {

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param array $config
     * @param LoggerInterface $logger
     */
    public function __construct(string $apikey, LoggerInterface $logger) {

        $this->apiKey = $apikey;
     
        $this->logger = $logger;
    }
 
        /**
     * @inheritDoc
     */
    public function send(array $to, ContactInterface $from, string $subject, $content) {

        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);

        $config = Configuration::getDefaultConfiguration()->setApiKey('partner-key', $this->apiKey);

        $apiInstance = new TransactionalEmailsApi(

            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new Client(),
            $config
        );

        $toArray = [];
        for ($cont = 0; count($to) > $cont; $cont++) {
            array_push($toArray, ['name' => $to[$cont]->getName(), 'email' => $to[$cont]->getEmail()]);
        }

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => $subject,
            'sender' => ['name' => $from->getName(), 'email' =>$from->getEmail()],
            'to' => $toArray,
            'htmlContent' => $content,
            //'params' => ['bodyMessage' => 'made just for you!']
        ]); // \Brevo\Client\Model\SendSmtpEmail | Values to send a transactional email

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}