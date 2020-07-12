<?php
declare(strict_types=1);

namespace App\Module\User\Mail\adapter;

use App\Mail\adapter\GoogleMailer;
use App\Module\User\Mail\UserMailerInterface;

/**
 * Class UserGoogleMailer
 * @package App\Module\User\Mail\adapter
 */
class UserGoogleMailer extends GoogleMailer implements UserMailerInterface { }