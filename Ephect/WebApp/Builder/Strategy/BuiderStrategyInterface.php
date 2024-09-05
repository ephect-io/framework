<?php

namespace Ephect\WebApp\Builder\Strategy;

interface BuiderStrategyInterface
{
    public function build(string $route): void;
}