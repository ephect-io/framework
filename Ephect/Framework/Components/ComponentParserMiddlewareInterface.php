<?php

namespace Ephect\Framework\Components;

interface ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props): void;

}