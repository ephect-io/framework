<?php

namespace Ephect\Modules\Forms\Events;

use Ephect\Framework\Event\Event;

class ComponentFinishedEvent extends Event
{
    public function __construct(
        private readonly string $cacheFilename,
        private readonly string $componentName,
        private readonly string $componentText,
    ) {
    }

    public function getCacheFilename(): string
    {
        return $this->cacheFilename;
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function getComponentText(): string
    {
        return $this->componentText;
    }
}