<?php

namespace Ephect\WebApp\Builder\Copiers\Strategy;

interface CopierStrategyInterface
{
    public function copy(string $path, string $key, string $filename): void;

}