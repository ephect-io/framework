<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;

class ComponentFactory
{
    public static function create(string $fullyQualifiedName, string $motherUID): FileComponentInterface
    {

        $filename = ComponentRegistry::read($fullyQualifiedName);
        $isPlugin = empty($filename) && !empty($filename = PluginRegistry::read($fullyQualifiedName));

        if ($isPlugin) {
            $uid = PluginRegistry::read($filename);
            $plugin = new Plugin($uid, $motherUID);
            $plugin->load($filename);

            return $plugin;
        }

        $uid = ComponentRegistry::read($filename);
        $comp = new Component($uid, $motherUID);
        $comp->load($filename);

        return $comp;
    }
}
