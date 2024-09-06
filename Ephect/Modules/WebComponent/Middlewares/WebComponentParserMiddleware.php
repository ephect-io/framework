<?php

namespace Ephect\Modules\WebComponent\Middlewares;

use Ephect\Framework\Registry\StateRegistry;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;
use function Ephect\Hooks\useState;

class WebComponentParserMiddleware implements ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void
    {
        StateRegistry::load(true);
        useState(["middlewares" => [WebComponentBuilderMiddleware::class => (object)$arguments],]);
//        StateRegistry::saveByMotherUid($motherUID);
        StateRegistry::save(true);
    }
}