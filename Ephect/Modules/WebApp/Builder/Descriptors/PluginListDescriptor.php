<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Utils\File;

class PluginListDescriptor implements ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new PluginDescriptor();
        $pluginList = File::walkTreeFiltered(\Constants::PLUGINS_ROOT, ['phtml']);
        foreach ($pluginList as $key => $pluginFile) {
            $descriptor->describe(\Constants::PLUGINS_ROOT, $pluginFile);
        }

        return $result;
    }
}
