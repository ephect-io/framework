<?php

namespace FunCom\Plugins\Router;

use FunCom\Components\Builders\AbstractBuilder;

class RouteBuilder extends AbstractBuilder
{

    public function build(): RouteInterface
    {
        return parent::buildEx((new RouteEntity())->getFullType());
    }

}