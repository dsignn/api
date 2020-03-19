<?php
declare(strict_types=1);

namespace App\Mail;

/**
 * Interface MailerInterface
 * @package App\Mail
 */
interface MailerInterface {

    /**
     * @param array $to
     * @param $content
     * @return mixed
     */
    public function send(array $to, $content);
}