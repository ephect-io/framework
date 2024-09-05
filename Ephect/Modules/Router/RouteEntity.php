<?php

namespace Ephect\Modules\Routing;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Entity\Entity;

class RouteEntity extends Entity implements RouteInterface
{
    use ElementTrait;

    private string $method = '';
    private string $rule = '';
    private string $normalized = '';
    private string $redirect = '';
    private string $translation = '';
    private int $error = 0;
    private bool $exact = false;
    private array $middlewares = [];

    public function __construct(RouteStructure $struct)
    {
        parent::__construct($struct);

        $this->method = $struct->method;
        $this->rule = $struct->rule;
        $this->normalized = $struct->normalized;
        $this->redirect = $struct->redirect;
        $this->translation = $struct->translation;
        $this->error = (int)$struct->error;
        $this->exact = $struct->exact !== 'true' ?: true;
        $this->middlewares = $struct->middlewares;

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

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }


}
