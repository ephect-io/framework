<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Plugin;
use Ephect\Modules\Forms\Registry\PluginRegistry;

class PluginDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        $plugin = new Plugin();
        $plugin->load($filename);
        $plugin->analyse();

        PluginRegistry::write($filename, $plugin->getUID());
        PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());

        return [$plugin->getFullyQualifiedFunction(), $plugin];
    }
}