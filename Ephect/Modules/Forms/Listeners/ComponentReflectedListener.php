<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Modules\Forms\Events\ComponentReflectedEvent;

use function Ephect\Hooks\useMemory;

class ComponentReflectedListener implements EventListenerInterface
{
    /**
     * @param Event|ComponentFinishedEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|ComponentReflectedEvent $event): void
    {
        [$reflectedComponents] = useMemory(get: 'reflectedComponents');
        $reflectedComponents = $reflectedComponents ?? [];
        $reflectedComponents[] = $event->getComponent()->getUID();
        useMemory(['reflectedComponents' => $reflectedComponents]);
    }
}
