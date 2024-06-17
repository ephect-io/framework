<?php

namespace Ephect\Framework\Components;

interface ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface $parent, string $motherUID, string $funcName, string $props): void;

}