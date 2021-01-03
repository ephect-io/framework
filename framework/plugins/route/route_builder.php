<?php

namespace FunCom\Plugins\Route;

use FunCom\Components\Builders\AbstractBuilder;

class RouteBuilder extends AbstractBuilder
{

    public function __construct(object $props)
    {
        parent::__construct($props, ['method', 'rule', 'redirect']);
    }

    public function build(): RouteInterface
    {
        return parent::buildEx(RouteEntity::class);
    }

}