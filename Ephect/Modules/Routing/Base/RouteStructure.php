<?php

namespace Ephect\Modules\Routing\Base;

use Ephect\Framework\Structure\Structure;

class RouteStructure extends Structure
{
    public string $uid = '';
    public string $method = '';
    public string $rule = '';
    public string $normalized = '';
    public string $redirect = '';
    public string $translation = '';
    public string $error = '';
    public string $exact = '';
    public array $middlewares = [];
}
