<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\Element;
use Ephect\Framework\ElementTrait;

class RouteEntity extends Element implements RouteInterface
{
    use ElementTrait;

    private string $method = '';
    private string $rule = '';
    private string $normalized = '';
    private string $redirect = '';
    private string $translation = '';
    private int $error = 0;
    private bool $exact = false;

    public function __construct(RouteStructure $struct)
    {
        $this->method = $struct->method;
        $this->rule = $struct->rule;
        $this->normalized = $struct->normalized;
        $this->redirect = $struct->redirect;
        $this->translation = $struct->translation;
        $this->error = (int) $struct->error;
        $this->exact = $struct->exact !== 'true' ?: true;

    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getNormalized(): string
    {
        return $this->normalized;
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

    public function isExact(): bool
    {
        return $this->exact;
    }
 }
