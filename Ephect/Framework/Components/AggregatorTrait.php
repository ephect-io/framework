<?php

namespace Ephect\Framework\Components;

trait AggregatorTrait
{
    protected array $list = [];
    protected function add(string $filename, string $className): void
    {
        $this->list[] = [$filename, $className];
    }
}