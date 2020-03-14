<?php
declare(strict_types=1);

namespace App\Auth;

/**
 * Interface RoleInterface
 * @package App\Auth
 */
interface RoleInterface {

    /**
     * @return string
     */
    public function getRole(): string;
}