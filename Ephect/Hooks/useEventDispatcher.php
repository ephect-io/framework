<?php

namespace Ephect\Hooks;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Registry\MemoryRegistry;

function useEventDispatcher(Event $event): void
{
    $events = MemoryRegistry::item('events');
    $eventProvider = $events && isset($events['eventProvider']) ? $events['eventProvider'] : null;
    $dispatcher = $eventProvider ? $eventProvider->getDispatcher() : null;

    $dispatcher->dispatch($event);
}