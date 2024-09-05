<?php

namespace Ephect\Apps\Builder\Descriptors;

use Ephect\Framework\Utils\File;

class PluginListDescriptor implements ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new PluginDescriptor;
        $pluginList = File::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
        foreach ($pluginList as $key => $pluginFile) {
            $descriptor->describe(PLUGINS_ROOT, $pluginFile);
        }

        return $result;
    }
}