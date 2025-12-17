<?php

namespace Ephect\Framework\Event;

interface StoppableEventInterface
{
    public function isPropagationStopped(): bool;
}
