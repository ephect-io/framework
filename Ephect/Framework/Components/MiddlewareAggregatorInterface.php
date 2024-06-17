<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Components\FileComponentInterface;

interface MiddlewareAggregatorInterface
{
    public function aggregateMiddlewares(): void;
}