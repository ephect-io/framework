<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Modules\Forms\Events\PageFinishedEvent;

class PageFinishedListener implements EventListenerInterface
{
    /**
     * @param Event|PageFinishedEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|PageFinishedEvent $event): void
    {
    }
}
