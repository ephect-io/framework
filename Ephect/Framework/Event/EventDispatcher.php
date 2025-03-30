<?php

namespace Ephect\Framework\Event;

use function Ephect\Hooks\useState;

class EventDispatcher implements EventDispatcherInterface
{

    private iterable $listeners = [];

    /**
     * @param StoppableEventInterface $event
     * @return StoppableEventInterface
     */
    public function dispatch(StoppableEventInterface $event): StoppableEventInterface
    {

        $eventListeners = $this->getListenersForEvent($event);
        foreach ($eventListeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) { //
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
        [$events] = useState(get: 'events');

        if ($events === null) {
            return [];
        }

        if (count($this->listeners) === 0) {
            foreach ($events as $eventClass => $listeners) {
                foreach (array_unique($listeners) as $listener) {
                    $this->addListener($eventClass, new $listener());
                }
            }
        }

        $eventName = get_class($event);
        if (array_key_exists($eventName, $this->listeners)) {
            return $this->listeners[$eventName];
        }

        return [];
    }

    /**
     * @param string $eventName
     * @param callable $listener
     * @return $this
     */
    public function addListener(string $eventName, callable $listener): EventDispatcher
    {
        $this->listeners[$eventName][] = $listener;

        return $this;
    }
}
