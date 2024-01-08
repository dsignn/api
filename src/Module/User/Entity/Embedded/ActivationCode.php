<?php
declare(strict_types=1);

namespace App\Module\User\Entity\Embedded;

/**
 * Class ActivationCode
 * @package App\Module\User\Entity\Embedded
 */
class ActivationCode {

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $token = '';

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param $date
     * @return ActivationCode
     */
    public function setDate(\DateTime $date = null): ActivationCode {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param $token
     * @return ActivationCode
     */
    public function setToken($token): ActivationCode {
        $this->token = $token;
        return $this;
    }
}