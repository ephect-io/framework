<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;

use function Ephect\Hooks\useMemory;

class ComponentFinishedListener implements EventListenerInterface
{
    /**
     * @param Event|ComponentFinishedEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|ComponentFinishedEvent $event): void
    {
        [$finishedComponents] = useMemory(get: 'finishedComponents');
        $finishedComponents = $finishedComponents ?? [];
        $finishedComponents[] = $event->getComponent()->getUID();
        useMemory(['finishedComponents' => $finishedComponents]);
    }
}
