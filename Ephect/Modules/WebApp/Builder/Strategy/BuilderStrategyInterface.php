<?php

namespace Ephect\Modules\WebApp\Builder\Strategy;

interface BuilderStrategyInterface
{
    public function build(string $route): void;
}
