<?php

namespace Ephect\Modules\Forms\Events;

use Ephect\Framework\Event\Event;
use Ephect\Modules\Forms\Components\ComponentInterface;

class ComponentReflectedEvent extends Event
{
    public function __construct(
        private readonly ComponentInterface $component,
        private readonly string $componentName,
        private readonly array $attributes,
        private readonly array $parameters,
        private readonly string $html = ''
    ) {
    }

    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }
  
    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getHtml(): string
    {
        return $this->component->getCode();
    }
  
}