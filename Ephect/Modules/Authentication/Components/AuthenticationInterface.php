<?php

namespace Ephect\Modules\Authentication\Components;

interface AuthenticationInterface
{
    public function getAuthId(): int | string;
    public function getEmail(): string;
    public function getPassword(): string;

}
