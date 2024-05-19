<?php

namespace Ephect\Framework\Components;

interface MiddlewareAggregatorInterface
{
    public function aggregateMiddlewares(): void;
}