<?php

namespace Ephect\Modules\Authentication\Repositories;

use Ephect\Modules\Authentication\Components\AuthenticationInterface;

interface AuthenticationRepositoryInterface
{
    public function findByEmail(string $email): ?AuthenticationInterface;
}
