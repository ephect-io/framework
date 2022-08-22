<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\Core\Structure;

class RouteStructure extends Structure
{
    public string $method = '';
    public string $rule = '';
    public string $normalized = '';
    public string $redirect = '';
    public string $translation = '';
    public string $error = '';
    public string $exact = '';
}