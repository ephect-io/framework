<?php

namespace Ephect\Framework\Event;

abstract class Event implements StoppableEventInterface
{
    private bool $progpagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->progpagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->progpagationStopped = true;
    }
}
