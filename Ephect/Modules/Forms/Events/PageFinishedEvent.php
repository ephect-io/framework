<?php

namespace Ephect\Modules\Forms\Events;

use Ephect\Framework\Event\Event;

class PageFinishedEvent extends Event
{
    public function __construct(
        private readonly string $motherUID,
        private readonly string $cacheFilename,
        private readonly string $componentName,
        private readonly object $props,
    ) {
    }

    public function getMotherUID(): string
    {
        return $this->motherUID;
    }

    public function getCacheFilename(): string
    {
        return $this->cacheFilename;
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function getProps(): object
    {
        return $this->props;
    }
}