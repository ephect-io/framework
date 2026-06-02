<?php

namespace Ephect\Plugins\Authentication\Repositories;

use Ephect\Plugins\Authentication\Components\AuthenticationInterface;

interface AuthenticationRepositoryInterface
{
    public function findByEmail(string $email): ?AuthenticationInterface;
}
