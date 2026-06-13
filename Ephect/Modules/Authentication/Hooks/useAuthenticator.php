<?php

namespace Ephect\Modules\Authentication\Hooks;

use Ephect\Modules\Authentication\Repositories\UserRepository;
use Ephect\Modules\HttpStorage\Session\Session;
use Ephect\Modules\Authentication\Components\Authenticator;
use Ephect\Modules\Authentication\Components\AuthenticatorInterface;

use function Ephect\Modules\DoctrineBridge\Hooks\useConnection;

function useAuthenticator(): AuthenticatorInterface
{
    $connection = useConnection();
    $session = new Session();
    $userRepository = new UserRepository($connection);
    $authenticator = new Authenticator($userRepository, $session);
    
    return $authenticator;
}