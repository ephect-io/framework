<?php

namespace Ephect\Forms\Middlewares;

use Ephect\Forms\Components\ComponentEntityInterface;

interface ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void;

}