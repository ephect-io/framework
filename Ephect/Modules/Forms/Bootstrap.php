<?php

namespace Ephect\Modules\Forms;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Event\Service\EventServiceProvider;
use Ephect\Framework\Modules\ModuleBootstrapInterface;
use Ephect\Modules\Forms\Events\ComponentAttributesEvent;
use Ephect\Modules\Forms\Events\ComponentFinishedEvent;
use Ephect\Modules\Forms\Events\PageFinishedEvent;
use Ephect\Modules\Forms\Listeners\ComponentAttributesListener;
use Ephect\Modules\Forms\Listeners\ComponentFinishedListener;
use Ephect\Modules\Forms\Listeners\PageFinishedListener;

class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {
        $dispatcher = new EventDispatcher();

        $provider = new EventServiceProvider(
            $dispatcher,
            [
                PageFinishedEvent::class => [
                    PageFinishedListener::class,
                ],
                ComponentFinishedEvent::class => [
                    ComponentFinishedListener::class,
                ],
                ComponentAttributesEvent::class => [
                    ComponentAttributesListener::class,
                ],
            ],
        );

        $provider->register();
    }
}
