<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\ElementUtils;
use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Utils\File;
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
        Logger::create()->info("Saving Page result as static file %s", $event->getComponentName());
        $staticFilename = \Constants::STATIC_DIR . ElementUtils::getBasenameFromFQClassName($event->getComponentName()) . \Constants::HTML_EXTENSION;
        File::safeWrite($staticFilename, $event->getHtml());    

    }
}
