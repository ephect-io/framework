<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Components\FileComponentInterface;

interface PluginWithMiddlewareInterface
{
    public function aggregateMiddlewares(): void;
}