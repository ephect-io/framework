<?php

namespace Ephect\Apps\Builder\Copiers\Strategy;

interface CopierStrategyInterface
{
    public function copy(string $path, string $key, string $filename): void;

}