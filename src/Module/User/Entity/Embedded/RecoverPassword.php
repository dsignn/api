<?php
declare(strict_types=1);

namespace App\Module\User\Entity\Embedded;

/**
 * Class RecoverPassword
 * @package App\Module\User\Entity\Embedded
 */
class RecoverPassword {

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
     * @return RecoverPassword
     */
    public function setDate(\DateTime $date = null): RecoverPassword {
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
     * @return RecoverPassword
     */
    public function setToken($token): RecoverPassword {
        $this->token = $token;
        return $this;
    }
}