<?php

namespace Ephect\Plugins\Route\Attributes;

use Attribute;
use Ephect\Framework\Components\Generators\TokenParsers\Middleware\MiddlewareAttributeInterface;
use Ephect\Plugins\Route\Middlewares\RouteParserMiddleware;

#[Attribute(Attribute::TARGET_FUNCTION)]
class RouteMiddleware implements MiddlewareAttributeInterface
{
    public function getMiddlewares(): array
    {
        return  [RouteParserMiddleware::class];
    }
}
