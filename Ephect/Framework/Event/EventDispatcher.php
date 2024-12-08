<?php

namespace Ephect\Framework\Event;

class EventDispatcher implements EventDispatcherInterface
{

    private iterable $listeners = [];

    public function dispatch(StoppableEventInterface $event): StoppableEventInterface
    {
        // TODO: Implement dispatch() method.
        $eventListeners = $this->getListenersForEvent($event);
        foreach ($eventListeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }

            $listener($event);
        }

        return $event;
    }

    /**
     * @param StoppableEventInterface $event
     *   An event for which to return the relevant listeners.
     * @return iterable<callable>
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(StoppableEventInterface $event): iterable
    {
        $eventName = get_class($event);
        if (array_key_exists($eventName, $this->listeners)) {
            return $this->listeners[$eventName];
        }

        return [];
    }

    public function addListener(string $eventName, callable $listener): EventDispatcher
    {
        $this->listeners[$eventName][] = $listener;

        return $this;
    }
}
