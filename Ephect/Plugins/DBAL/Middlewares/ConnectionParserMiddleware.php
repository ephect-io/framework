<?php

namespace Ephect\Plugins\DBAL\Middlewares;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Components\ComponentParserMiddlewareInterface;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class ConnectionParserMiddleware implements ComponentParserMiddlewareInterface
{

    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props): void
    {
        $params = [$parent, $motherUID, $funcName, $props];
        $json = json_encode($params);

        $text = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite(CACHE_DIR . "ConnectionParserMiddleware.txt", $text);
    }
}