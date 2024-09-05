<?php

namespace Ephect\WebApp\Builder\Parsers;

use Ephect\Forms\Components\Plugin;
use Ephect\Forms\Registry\PluginRegistry;

class PluginParser implements ParserTypeInterface
{
    public function __construct(private readonly string $filename)
    {
    }

    public function parse(): array
    {
        $plugin = new Plugin();
        $plugin->load($this->filename);
        $plugin->analyse();

        PluginRegistry::write($this->filename, $plugin->getUID());
        PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());

        return [$plugin->getFullyQualifiedFunction(), $plugin];
    }
}