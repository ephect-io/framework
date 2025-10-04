<?php

namespace Ephect\Modules\DataAccess\Middlewares;

use Ephect\Framework\Event\EventDispatcher;
use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Modules\DataAccess\Events\ConnectionOpenerEvent;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;

use function Ephect\Hooks\useState;

class ConnectionParserMiddleware implements ComponentParserMiddlewareInterface
{
    public function parse(
        ComponentEntityInterface|null $parent,
        string $motherUID,
        string $funcName,
        string $props,
        array $arguments
    ): void {
        //        StateRegistry::saveByMotherUid($motherUID, true);
        useState(["middlewares" => [ConnectionOpenerMiddleware::class => (object)$arguments],]);
        $middleware = new ConnectionOpenerMiddleware();
        $middleware((object) $arguments);

        $eventsProvider = useState(get: 'eventsProvider');
        // TODO: Fix event dispatcher issue
        //        Logger::create()->dump('eventsProvider', $eventsProvider);
        //        $connectionEvent = new ConnectionOpenerEvent((object) $arguments);
        //        $dispatcher = new EventDispatcher();
        //        $dispatcher->dispatch($connectionEvent);

        //        StateRegistry::save(true);
    }
}
