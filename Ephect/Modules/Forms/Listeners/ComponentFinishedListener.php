<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;

class ComponentFinishedListener implements EventListenerInterface
{

    public function __invoke(Event|ComponentFinishedEvent $event): void
    {
        $storeFilename = STORE_DIR . DIRECTORY_SEPARATOR . $event->getCacheFilename();
        $text = $event->getComponentText();

        File::safeWrite($storeFilename, $text);
    }
}