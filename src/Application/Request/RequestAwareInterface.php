<?php
declare(strict_types=1);

namespace App\Application\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RequestAwarInteface
 * @package App\Application\Request
 */
interface RequestAwareInterface {

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function setRequest(ServerRequestInterface $request);
}