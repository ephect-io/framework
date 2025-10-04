<?php

namespace Ephect\Modules\WebApp\Builder\Strategy;

interface BuiderStrategyInterface
{
    public function build(string $route): void;
}
