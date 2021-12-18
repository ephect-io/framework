<?php

namespace Ephect\Plugins\Route;

use Ephect\Element;

class RouteEntity extends Element implements RouteInterface
{
    private string $method = '';
    private string $rule = '';
    private string $redirect = '';
    private string $translation = '';
    private int $error = 200;

    public function __construct(RouteStructure $struct)
    {
        $this->method = $struct->method;
        $this->rule = $struct->rule;
        $this->redirect = $struct->redirect;
        $this->translation = $struct->translation;
        $this->error = (int) $struct->error;

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

    public function getError(): int
    {
        return $this->error;
    }
 }
