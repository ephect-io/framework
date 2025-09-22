<?php

namespace Ephect\Modules\WebApp\Builder\Registerer;

interface RegistererInterface
{
    public function register(array $values): void;
}
