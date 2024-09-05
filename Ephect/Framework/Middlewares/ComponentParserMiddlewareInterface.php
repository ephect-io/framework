<?php

namespace Ephect\Framework\Middlewares;

use Ephect\Framework\Components\ComponentEntityInterface;

interface ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void;

}