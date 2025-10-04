<?php

namespace Ephect\Framework\Aggregators;

interface MiddlewareAggregatorInterface
{
    public function aggregateMiddlewares(): void;
}
