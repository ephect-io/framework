<?php

namespace Ephect\Modules\Forms\Listeners;

use Ephect\Framework\IO\Utils;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;
use Ephect\Modules\Forms\Events\ComponentFinishedEventInterface;

class ComponentFinishedListener implements ComponentFinishedEventInterface
{

    public function invoke(ComponentFinishedEvent $componentFinishedEvent): void
    {
        // TODO: Implement invoke() method.
        $storeFilename = str_replace(CACHE_DIR, STORE_DIR, $componentFinishedEvent->getCacheFilename());
        $storeDir = dirname($storeFilename);
        Utils::safeMkDir($storeDir);
        $text = $componentFinishedEvent->getComponentText();
        Utils::safeWrite($storeFilename, $text);
    }
}