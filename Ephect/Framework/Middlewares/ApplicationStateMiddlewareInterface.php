<?php

namespace Ephect\Framework\Middlewares;

interface ApplicationStateMiddlewareInterface
{
    public function __invoke(object $arguments);
}
