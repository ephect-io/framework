<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Plugin;
use Ephect\Modules\Forms\Registry\PluginRegistry;

class PluginDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        $relativeFile =
            str_replace(\Constants::EPHECT_ROOT, '', $sourceDir) .
            str_replace(pathinfo($filename, PATHINFO_EXTENSION), 'php', $filename);
        File::safeCopy($sourceDir . $filename, \Constants::COPY_DIR . $relativeFile);

        $plugin = new Plugin();
        $plugin->load($relativeFile);
        $plugin->analyse();

        PluginRegistry::write($filename, $plugin->getUID());
        PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());

        return [$plugin->getFullyQualifiedFunction(), $plugin];
    }
}