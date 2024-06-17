<?php

namespace Ephect\Framework\Components;

trait AggregatorTrait
{
    protected array $list = [];
    protected function add(string $className): void
    {
        $this->list[] = $className;
    }
}