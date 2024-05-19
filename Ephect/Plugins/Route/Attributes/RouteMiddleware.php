<?php

namespace Ephect\Plugins\Route\Attributes;

use Attribute;
use Ephect\Framework\Components\Generators\TokenParsers\Middleware\MiddlewareAttributeInterface;
use Ephect\Plugins\Route\RouteParserMiddleware;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_FUNCTION)]
class RouteMiddleware implements MiddlewareAttributeInterface
{
    public function __construct()
    {
    }

    public function getMiddlewares(): array
    {
        return  [RouteParserMiddleware::class];
    }
}
