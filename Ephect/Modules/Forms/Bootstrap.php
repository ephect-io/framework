<?php

namespace Ephect\Modules\Forms;

use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Modules\ModuleBootstrapInterface;
use Ephect\Modules\Forms\Events\ComponentAttributesEvent;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;
use Ephect\Modules\Forms\Events\PageFinishedEvent;
use Ephect\Modules\Forms\Listeners\ComponentAttributesListener;
use Ephect\Modules\Forms\Listeners\ComponentFinishedListener;
use Ephect\Modules\Forms\Listeners\PageFinishedListener;

use function Ephect\Hooks\useEvents;

class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {

        Logger::create()->info("Booting Forms module");
        [$eventProvider] = useEvents(get: 'eventProvider');

        $eventProvider->addListeners([
            PageFinishedEvent::class => [
                PageFinishedListener::class,
            ],
            ComponentFinishedEvent::class => [
                ComponentFinishedListener::class,
            ],
            ComponentAttributesEvent::class => [
                ComponentAttributesListener::class,
            ],
        ]);

        $eventProvider->register();

        useEvents($eventProvider);
    }
}
