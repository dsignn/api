<?php
declare(strict_types=1);

namespace App\Mail;


class Contact implements ContactInterface {

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param $email
     * @return Contact
     */
    public function setEmail($email): Contact {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param $name
     * @return Contact
     */
    public function setName($name): Contact {
        $this->name = $name;
        return $this;
    }
}