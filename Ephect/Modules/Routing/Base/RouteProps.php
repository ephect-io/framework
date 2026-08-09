<?php

namespace Ephect\Modules\Routing\Base;

use Ephect\Modules\Forms\Components\ComponentProps;

class RouteProps extends ComponentProps
{
    public string $method = '';
    public string $rule = '';
    public string $redirect = '';
    public int $error = 0;
    public bool $exact = false;
}