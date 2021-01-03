<?php

namespace FunCom\Plugins\Route;

use FunCom\ElementInterface;

interface RouteInterface extends ElementInterface
{
    public function getMethod(): string;
    public function getRule(): string;
    public function getRedirect(): string;
}