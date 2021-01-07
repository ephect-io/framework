<?php

namespace FunCom\Plugins\Route;

use FunCom\Element;

class RouteEntity extends Element implements RouteInterface
{
    private string $method = '';
    private string $rule = '';
    private string $redirect = '';

    public function __construct(string $method = '', string $rule = '', string $redirect = '')
    {
        $this->method = $method;
        $this->rule = $rule;
        $this->redirect = $redirect;
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
