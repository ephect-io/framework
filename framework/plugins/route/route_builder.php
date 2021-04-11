<?php

namespace Ephect\Plugins\Route;

use Ephect\Components\Builders\AbstractBuilder;

class RouteBuilder extends AbstractBuilder
{

    public function __construct(object $props)
    {
        parent::__construct($props, RouteStructure::class);
    }

    public function build(): RouteInterface
    {
        return parent::buildEx(RouteEntity::class);
    }

}