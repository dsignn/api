<?php
declare(strict_types=1);

namespace App\Mail;

/**
 * Interface MailerInterface
 * @package App\Mail
 */
interface MailerInterface {

    /**
     * @param array<ContactInterface> $to
     * @param ContactInterface $from
     * @param string $subject
     * @param $content
     * @return MailerInterface
     */
    public function send(array $to, ContactInterface $from, string $subject, $content);
}