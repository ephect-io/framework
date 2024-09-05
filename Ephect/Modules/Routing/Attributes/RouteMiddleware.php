<?php

namespace Ephect\Modules\Routing\Attributes;

use Attribute;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Modules\Routing\Middlewares\RouteParserMiddleware;

#[Attribute(Attribute::TARGET_FUNCTION)]
class RouteMiddleware implements AttributeMiddlewareInterface
{
    public function getMiddlewares(): array
    {
        return [RouteParserMiddleware::class];
    }
}
