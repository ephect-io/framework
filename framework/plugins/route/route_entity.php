<?php

namespace Ephect\Plugins\Route;

use Ephect\Element;

class RouteEntity extends Element implements RouteInterface
{
    private $method = '';
    private $rule = '';
    private $redirect = '';

    public function __construct(RouteStructure $struct)
    {
        $this->method = $struct->method;
        $this->rule = $struct->rule;
        $this->redirect = $struct->redirect;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getRedirect(): string
    {
        return $this->redirect;
    }
}
