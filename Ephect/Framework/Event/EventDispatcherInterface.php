<?php

namespace Ephect\Framework\Event;

interface EventDispatcherInterface
{
    public function dispatch(StoppableEventInterface $event): StoppableEventInterface;
}
