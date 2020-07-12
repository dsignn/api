<?php
declare(strict_types=1);

namespace App\Mail;

/**
 * Interface ContactInterface
 * @package App\Mail
 */
interface ContactInterface {

    /**
     * @return string
     */
    function getEmail(): string;

    /**
     * @return string
     */
    function getName(): string;
}