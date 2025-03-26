<?php

namespace Ephect\Framework\Event\Service;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Services\ServiceProviderInterface;
use function Ephect\Hooks\useState;

class EventServiceProvider implements ServiceProviderInterface
{

    /**
     * @param EventDispatcher $dispatcher
     * @param array<array<string, EventListenerInterface>> $eventListeners
     */
    public function __construct(
        private readonly EventDispatcher $dispatcher,
        private readonly array $eventListeners = [],
    ) {
    }

    /**
     * @return void
     */
    public function register(): void
    {
        [$events, $setState] = useState(get: 'events');

        foreach ($this->eventListeners as $eventClass => $listeners) {
            $events[$eventClass] = $listeners;
            foreach (array_unique($listeners) as $listener) {
                $this->dispatcher->addListener($eventClass, new $listener());
            }
        }

        $setState(['events' => $events]);
    }
}
