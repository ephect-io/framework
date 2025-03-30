<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Plugin;
use Ephect\Modules\Forms\Registry\PluginRegistry;

class PluginDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        $relativeDir = str_replace(\Constants::EPHECT_ROOT, '', $sourceDir);
        File::safeCopy($sourceDir . $filename, \Constants::COPY_DIR . $relativeDir . $filename);

        $plugin = new Plugin();
        $plugin->load($relativeDir . $filename);
        $plugin->analyse();

        PluginRegistry::write($filename, $plugin->getUID());
        PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());

        return [$plugin->getFullyQualifiedFunction(), $plugin];
    }
}