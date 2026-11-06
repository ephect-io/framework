<?php

namespace Ephect\Modules\WebApp\Builder\Strategy;

interface BuiderStrategyInterface
{
    public function build(array|string|null $routeItem): void;
}
