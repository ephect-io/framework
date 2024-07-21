<?php

namespace Ephect\Plugins\Route\Attributes;

use Attribute;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Plugins\Route\Middlewares\RouteParserMiddleware;

#[Attribute(Attribute::TARGET_FUNCTION)]
class RouteMiddleware implements AttributeMiddlewareInterface
{
    public function getMiddlewares(): array
    {
        return  [RouteParserMiddleware::class];
    }
}
