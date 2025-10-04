<?php

namespace Ephect\Modules\DataAccess\Listeners;

use Ephect\Framework\Event\Event;
use Ephect\Framework\Event\EventListenerInterface;
use Ephect\Modules\DataAccess\Client\PDO\PdoConnection;
use Ephect\Modules\DataAccess\Events\ConnectionOpenerEvent;
use Ephect\Modules\Forms\Events\PageFinishedEvent;

use function Ephect\Hooks\useMemory;

class ConnectionOpenerListener implements EventListenerInterface
{
    /**
     * @param Event|PageFinishedEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function __invoke(Event|ConnectionOpenerEvent $event): void
    {
        $arguments = $event->getArguments();
        $conn = PdoConnection::opener($arguments->conf);
        useMemory(["$arguments->conf" => $conn,]);
    }
}
