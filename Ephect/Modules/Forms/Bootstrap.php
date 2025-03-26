<?php

namespace Ephect\Modules\Forms;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\Service\EventServiceProvider;
use Ephect\Framework\Modules\ModuleBootstrapInterface;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;
use Ephect\Modules\Forms\Listeners\ComponentFinishedListener;

class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {
        $dispatcher = new EventDispatcher();

        $provider = new EventServiceProvider(
            $dispatcher,
            [
                ComponentFinishedEvent::class => [
                    ComponentFinishedListener::class
                ],
            ],
        );

        $provider->register();
    }
}
