<?php

namespace Ephect\Framework\Event;

interface EventListenerInterface
{
    public function __invoke(Event $event): void;
}
