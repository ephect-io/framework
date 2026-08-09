<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Logger\Logger;
use Ephect\Modules\Forms\Events\ComponentAlreadyMadeEvent;
use Ephect\Modules\Forms\Registry\ComponentRegistry;

class ComponentAlreadyMadeListener implements EventListenerInterface
{
    /**
     * @param Event|ComponentAlreadyMadeEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|ComponentAlreadyMadeEvent $event): void
    {
        /*
         * When a component is already made, we want to be warned of it.
         */
        $fqComponentName = ComponentRegistry::read($event->getComponent()->getUID());
        Logger::create()->info("Finished component %s", $fqComponentName);
    }
}
