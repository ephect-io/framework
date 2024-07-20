<?php

namespace Ephect\Apps\Builder\Strategy;

interface BuiderStrategyInterface
{
    public function build(string $route): void;
}