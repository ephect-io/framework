<?php

namespace Ephect\Plugins\DBAL\Attributes;

use Attribute;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Plugins\DBAL\Middlewares\ConnectionParserMiddleware;

#[Attribute(Attribute::TARGET_FUNCTION)]
class ConnectionMiddleware implements AttributeMiddlewareInterface
{

    public function __construct(
        private string $conf
    )
    {

    }

    public function getMiddlewares(): array
    {
        return [
            ConnectionParserMiddleware::class,
        ];
    }
}