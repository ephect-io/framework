<?php

namespace Ephect\Plugins\WebComponent\Attributes;

use Attribute;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Plugins\WebComponent\Middlewares\WebComponentParserMiddleware;

#[Attribute(Attribute::TARGET_FUNCTION)]
class WebComponentZeroConf implements AttributeMiddlewareInterface
{

    public function getMiddlewares(): array
    {
        return [
            WebComponentParserMiddleware::class,
        ];
    }
}