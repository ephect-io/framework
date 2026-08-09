<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\MemoryRegistry;
use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\Service\EventServiceProvider;

/**
 * @param array|EventServiceProvider|null $events
 * @param string $get
 * @return array
 * @throws \InvalidArgumentException
 */
function useEvents(array|EventServiceProvider|null $events = null, string $get = ''): array
{
    if ($events !== null && $get !== '') {
        throw new \InvalidArgumentException(
            "You can't store an event object in memory and get an indexed value at once."
        );
    }

    $setEvents = function (array|EventServiceProvider $events): void {
        if (!is_array($events) && !($events instanceof EventServiceProvider)) {
            throw new \InvalidArgumentException(
                "The events parameter must be an instance of EventServiceProvider or an array of events."
            );
        }

        if (is_array($events)) {
            MemoryRegistry::writeItem('events', ['events' => $events]);
        } elseif ($events instanceof EventServiceProvider) {
            MemoryRegistry::writeItem('events', ['eventProvider' => $events]);
        }
    };

    if ($events !== null) {
        $setEvents($events);
    } else {
        $events = MemoryRegistry::item('events');
        if ($get == 'events') {
            $events = $events && isset($events['events']) ? $events['events'] : [];
        } elseif ($get == 'eventProvider') {
            $events = $events && isset($events['eventProvider']) ? $events['eventProvider'] : null;
            $events ??= new EventServiceProvider(
                new EventDispatcher(),
            );
        } elseif ($get == 'eventDispatcher') {
            $eventProvider = $events && isset($events['eventProvider']) ? $events['eventProvider'] : null;
            $events = $eventProvider ? $eventProvider->getDispatcher() : null;
        }
    }

    return [$events, $setEvents];
}
