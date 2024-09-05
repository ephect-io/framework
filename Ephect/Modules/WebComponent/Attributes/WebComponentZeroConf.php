<?php

namespace Ephect\Modules\WebComponent\Attributes;

use Attribute;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Modules\WebComponent\Middlewares\WebComponentParserMiddleware;

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