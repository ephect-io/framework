<?php

namespace Ephect\Plugins\Route;

use Ephect\Element;

class RouteEntity extends Element implements RouteInterface
{
    private $method = '';
    private $rule = '';
    private $redirect = '';
    private $translation = '';

    public function __construct(RouteStructure $struct)
    {
        $this->method = $struct->method;
        $this->rule = $struct->rule;
        $this->redirect = $struct->redirect;
        $this->translation = $struct->translation;
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

    public function getTranslation(): string
    {
        return $this->translation;
    }
}
