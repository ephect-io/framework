<?php

namespace Ephect\Plugins\Route;

use Ephect\ElementInterface;

interface RouteInterface extends ElementInterface
{
    public function getMethod(): string;
    public function getRule(): string;
    public function getRedirect(): string;
    public function getTranslation(): string;
}