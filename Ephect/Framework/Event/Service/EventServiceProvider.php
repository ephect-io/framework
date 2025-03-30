<?php

namespace Ephect\Framework\Event\Service;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Services\ServiceProviderInterface;

use function Ephect\Hooks\useState;

readonly class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * @param EventDispatcher $dispatcher
     * @param array<class-string, array<class-string>> $eventListeners
     */
    public function __construct(
        private EventDispatcher $dispatcher,
        private array $eventListeners = [],
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
