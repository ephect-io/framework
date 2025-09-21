<?php

namespace Ephect\Modules\DataAccess;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\Service\EventServiceProvider;
use Ephect\Framework\Modules\ModuleBootstrapInterface;
use Ephect\Modules\DataAccess\Events\ConnectionOpenerEvent;
use Ephect\Modules\DataAccess\Listeners\ConnectionOpenerListener;

class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {
        $dispatcher = new EventDispatcher();

        $provider = new EventServiceProvider(
            $dispatcher,
            [
                ConnectionOpenerEvent::class => [
                    ConnectionOpenerListener::class,
                ],
            ],
        );

        $provider->register();
    }
}
