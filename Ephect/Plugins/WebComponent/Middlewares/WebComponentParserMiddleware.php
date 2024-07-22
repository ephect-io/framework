<?php

namespace Ephect\Plugins\WebComponent\Middlewares;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Registry\StateRegistry;
use function Ephect\Hooks\useState;

class WebComponentParserMiddleware
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void
    {
        useState(["middlewares" => [WebComponentBuilderMiddleware::class => (object) $arguments],]);
        StateRegistry::saveByMotherUid($motherUID, true);
    }
}