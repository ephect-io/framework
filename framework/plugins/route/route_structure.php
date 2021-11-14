<?php

namespace Ephect\Plugins\Route;

use Ephect\Core\Structure;

class RouteStructure extends Structure
{
    public string $method = '';
    public string $rule = '';
    public string $redirect = '';
    public string $translation = '';
}