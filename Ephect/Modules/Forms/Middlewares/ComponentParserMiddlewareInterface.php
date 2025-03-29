<?php

namespace Ephect\Modules\Forms\Middlewares;

use Ephect\Modules\Forms\Components\ComponentEntityInterface;

interface ComponentParserMiddlewareInterface
{
    public function parse(
        ComponentEntityInterface|null $parent,
        string $motherUID,
        string $funcName,
        string $props,
        array $arguments
    ): void;

}