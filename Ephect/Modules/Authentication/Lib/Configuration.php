<?php

namespace Ephect\Modules\Authentication\Common;

class Configuration
{
    public const AUTH_KEY = 'auth_id';

    private string $userProviderClass;
    private string $passwordHasherClass;

    public function __construct(string $userProviderClass, string $passwordHasherClass)
    {
        $this->userProviderClass = $userProviderClass;
        $this->passwordHasherClass = $passwordHasherClass;
    }

    public function getUserProviderClass(): string
    {
        return $this->userProviderClass;
    }

    public function getPasswordHasherClass(): string
    {
        return $this->passwordHasherClass;
    }
}