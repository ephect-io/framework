<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\Components\ComponentParserMiddlewareAggregatorTrait;
use Ephect\Framework\Components\PluginWithMiddlewareInterface;

class RouteService implements PluginWithMiddlewareInterface
{

    use ComponentParserMiddlewareAggregatorTrait;

    public function aggregateMiddlewares(): void
    {
        $this->add(__DIR__ . 'RouteParserMiddleware.php', RouteParserMiddleware::class);
        $this->aggregateComponentParserMiddlewares();
    }
}