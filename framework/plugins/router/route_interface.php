<?php

namespace FunCom\Plugins\Router;

use FunCom\ElementInterface;

interface RouteInterface extends ElementInterface
{
    public function getMethod(): string;
    public function getRule(): string;
    public function getRedirect(): string;
}