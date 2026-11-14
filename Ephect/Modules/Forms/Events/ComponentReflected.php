<?php

namespace Ephect\Modules\Forms\Events;

use Ephect\Framework\Event\Event;
use Ephect\Modules\Forms\Components\ComponentInterface;

class ComponentReflectedEvent extends Event
{
    public function __construct(
        private readonly ComponentInterface $component,
        private readonly string $cacheFilename,
        private readonly string $componentName,
        private readonly ?object $props,
        private readonly string $html = ''
    ) {
    }

    public function getCacheFilename(): string
    {
        return $this->cacheFilename;
    }

    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }
  
    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function getProps(): ?object
    {
        return $this->props;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
  
}