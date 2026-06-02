<?php

namespace Ephect\Plugins\Authentication\Components;

use Ephect\Framework\Session\SessionInterface;

interface AuthenticatorInterface
{
    public function authenticate(string $email, string $password): bool;

    public function login(AuthenticationInterface $user): void;

    public function logout(): void;

    public function getUser(): AuthenticationInterface;

    public function isAuthenticated(): bool;

    public static function hasLoggedIn(SessionInterface $session): bool;
}
