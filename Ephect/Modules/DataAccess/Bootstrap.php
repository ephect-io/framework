<?php

namespace Ephect\Modules\DataAccess;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\Service\EventServiceProvider;
use Ephect\Framework\Modules\ModuleBootstrapInterface;
use Ephect\Modules\DataAccess\Events\ConnectionOpenerEvent;
use Ephect\Modules\DataAccess\Listeners\ConnectionOpenerListener;

use function Ephect\Hooks\useEvents;

class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {
        [$eventProvider] = useEvents(get: 'eventProvider');

        $eventProvider->addListeners([
            ConnectionOpenerEvent::class => [
                ConnectionOpenerListener::class,
            ],
        ]);

        $eventProvider->register();

        useEvents($eventProvider);
    }
}
