<?php

namespace Ephect\Framework\Components;

interface AggregatorInterface
{
    public function add(string $className): void;
    public function aggregate(string $filename): void;

}