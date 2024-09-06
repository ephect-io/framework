<?php

namespace Ephect\Modules\WebApp\Builder\Parsers;

use Ephect\Modules\Forms\Components\Plugin;

class ParserFactory
{
    public static function createParser(string $moduleEntrypointClass, string $filename): ParserTypeInterface
    {
        return match ($moduleEntrypointClass) {
            Plugin::class => new PluginParser($filename),
            default => new ModuleParser($moduleEntrypointClass, $filename),
        };
    }
}