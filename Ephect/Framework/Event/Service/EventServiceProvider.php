<?php

namespace Ephect\Framework\Event\Service;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Services\ServiceProviderInterface;

use function Ephect\Hooks\useEvents;

class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * @param array<class-string, array<class-string>> $eventListeners
     */
    private array $eventListeners;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(
        private EventDispatcher $dispatcher,
    ) {
        $this->eventListeners = [];
    }

    public function getDispatcher(): EventDispatcher
    {
        return $this->dispatcher;
    }

    public function addListeners(array $listeners) {
        foreach ($listeners as $eventClass => $listenerClasses) {
            if (!is_array($listenerClasses)) {
                throw new \InvalidArgumentException(
                    "Listeners for event class '$eventClass' must be an array of listener classes."
                );
            }
            foreach ($listenerClasses as $listenerClass) {
                if (!is_string($listenerClass)) {
                    throw new \InvalidArgumentException(
                        "Listener class for event class '$eventClass' must be a string."
                    );
                }
            }
            $this->eventListeners[$eventClass] = $listenerClasses;
        }
    }

    /**
     * @return void
     */
    public function register(): void
    {
        [$events, $setMemory] = useEvents(get: 'events');

        foreach ($this->eventListeners as $eventClass => $listeners) {
            $events[$eventClass] = $listeners;
            foreach (array_unique($listeners) as $listener) {
                $this->dispatcher->addListener($eventClass, new $listener());
            }
        }

        $setMemory(['events' => $events]);
    }
}
