<?php

namespace Ephect\Modules\WebApp\Builder\Strategy;

interface BuilderStrategyInterface
{
    public function build(array|string|null $routeItem): void;
}
