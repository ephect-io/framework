<?php

namespace Ephect\Modules\DataAccess\Middlewares;

use Ephect\Forms\Components\ComponentEntityInterface;
use Ephect\Forms\Middlewares\ComponentParserMiddlewareInterface;
use Ephect\Framework\Registry\StateRegistry;
use function Ephect\Hooks\useState;

class ConnectionParserMiddleware implements ComponentParserMiddlewareInterface
{

    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void
    {
        StateRegistry::load(true);
        useState(["middlewares" => [ConnectionOpenerMiddleware::class => (object)$arguments],]);
//        StateRegistry::saveByMotherUid($motherUID, true);
        StateRegistry::save( true);
    }
}