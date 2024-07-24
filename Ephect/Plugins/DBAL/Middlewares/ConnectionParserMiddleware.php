<?php

namespace Ephect\Plugins\DBAL\Middlewares;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Middlewares\ComponentParserMiddlewareInterface;
use Ephect\Framework\Registry\StateRegistry;
use function Ephect\Hooks\useState;

class ConnectionParserMiddleware implements ComponentParserMiddlewareInterface
{

    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void
    {
        StateRegistry::load();
        useState(["middlewares" => [ConnectionOpenerMiddleware::class => (object) $arguments],]);
        StateRegistry::saveByMotherUid($motherUID, true);
    }
}