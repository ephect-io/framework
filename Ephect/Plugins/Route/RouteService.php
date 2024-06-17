<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\Components\ComponentParserMiddlewareAggregatorTrait;
use Ephect\Framework\Components\MiddlewareAggregatorInterface;

class RouteService implements MiddlewareAggregatorInterface
{

    use ComponentParserMiddlewareAggregatorTrait;

    public function aggregateMiddlewares(): void
    {
        $this->add(RouteParserMiddleware::class);
        $this->aggregateComponentParserMiddlewares();
    }
}