<?php

namespace Ephect\Framework\Aggregators;

interface AggregatorInterface
{
    public function add(string $className): void;
    public function aggregate(string $filename): void;

}