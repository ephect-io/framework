<?php

namespace Ephect\Modules\DataAccess\Middlewares;

use Ephect\Framework\Registry\StateRegistry;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;
use function Ephect\Hooks\useState;

class ConnectionParserMiddleware implements ComponentParserMiddlewareInterface
{

    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void
    {
        StateRegistry::saveByMotherUid($motherUID, true);
        useState(["middlewares" => [ConnectionOpenerMiddleware::class => (object)$arguments],]);
        StateRegistry::saveByMotherUid($motherUID, true);
//        StateRegistry::save( true);
    }
}