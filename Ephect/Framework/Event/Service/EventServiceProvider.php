<?php

namespace Ephect\Framework\Event\Service;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Services\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{

    /**
     * @param EventDispatcher $dispatcher
     * @param array<array<EventListenerInterface>> $eventListeners
     */
    public function __construct(
        private readonly EventDispatcher $dispatcher,
        private readonly array $eventListeners = [],
    ) {
    }

    public function register(): void
    {
        // TODO: Implement register() method.
        foreach ($this->eventListeners as $eventClass => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                $this->dispatcher->addListener($eventClass, new $listener());
            }
        }
    }
}
